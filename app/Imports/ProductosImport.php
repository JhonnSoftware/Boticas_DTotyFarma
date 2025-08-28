<?php

namespace App\Imports;

use App\Models\Productos;
use App\Models\Genericos;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ProductosImport implements
    ToModel,
    WithHeadingRow,
    SkipsEmptyRows,
    WithValidation,
    WithChunkReading,
    WithBatchInserts,
    WithUpserts
{
    /** Cache local para resolver genericos por nombre -> id */
    protected array $cacheGenericos = [];

    // === LÍMITES SUGERIDOS (ajusta si tu BD usa otros) ===
    private const MAX_TINY  = 255;
    private const MAX_SMALL = 65535;

    public function headingRow(): int { return 1; }
    public function chunkSize(): int   { return 500; }
    public function batchSize(): int   { return 500; }

    /** Upsert por combinación código + lote */
    public function uniqueBy() { return ['codigo', 'lote']; }

    public function model(array $row)
    {
        $g = fn(...$keys) => $this->firstNonEmpty($row, $keys);

        // --- Base ---
        $codigo       = trim((string) $g('codigo'));
        $descripcion  = trim((string) $g('descripcion'));

        // Presentación: NUNCA null -> "SIN PRESENTACION" si viene vacía/NULL/N/A/etc.
        $presentacion = $this->normPresentacion(
            $g('presentacion', 'presentación') ?? $this->firstByContains($row, ['present'])
        );

        $laboratorio  = $this->nullIfNullish($g('laboratorio'));
        $lote         = trim((string) $g('lote'));

        $fecha_vencimiento = $this->parseFecha((string) $g('fecha_vencimiento')) ?? Carbon::create(2099,12,31);

        // --- Conversión y stock (CLAMP) ---
        $u_blister         = $this->uInt($g('unidades_por_blister'), self::MAX_SMALL); // 0..65535
        $u_caja            = $this->uInt($g('unidades_por_caja'),    self::MAX_SMALL); // 0..65535

        $cantidad          = $this->uInt($g('cantidad'),          self::MAX_TINY);    // 0..255
        $cantidad_blister  = $this->uInt($g('cantidad_blister'),  self::MAX_TINY);    // 0..255
        $cantidad_caja     = $this->uInt($g('cantidad_caja'),     self::MAX_TINY);    // 0..255

        $stock_min         = $this->uInt($g('stock_minimo'),          self::MAX_SMALL);
        $stock_min_blister = $this->uInt($g('stock_minimo_blister'),  self::MAX_SMALL);
        $stock_min_caja    = $this->uInt($g('stock_minimo_caja'),     self::MAX_SMALL);

        // --- Descuentos y precios ---
        $desc_uni     = $this->toDecimal($g('descuento'));
        $desc_blister = $this->toDecimal($g('descuento_blister'));
        $desc_caja    = $this->toDecimal($g('descuento_caja'));

        $pc_uni       = $this->toDecimal($g('precio_compra'));
        $pc_blister   = $this->toDecimal($g('precio_compra_blister'));
        $pc_caja      = $this->toDecimal($g('precio_compra_caja'));

        // Localizar precio_venta aunque el header esté “sucio”
        $pv_uni_raw = $g('precio_venta') ?? $this->firstByContains($row, ['precio','venta']);
        $pv_uni     = $this->toDecimal($pv_uni_raw);
        $pv_blister = $this->toDecimal($g('precio_venta_blister'));
        $pv_caja    = $this->toDecimal($g('precio_venta_caja'));

        // Fallbacks seguros (evita NOT NULL)
        if ($pv_uni === null)     { $pv_uni = 0.0; }
        if ($pv_blister === null) { $pv_blister = $pv_uni; }
        if ($pv_caja === null)    { $pv_caja    = $pv_uni; }

        // --- Otros ---
        $foto         = $this->nullIfNullish($g('foto'));
        $id_proveedor = $this->uInt($g('id_proveedor'), self::MAX_SMALL);
        $id_clase     = $this->uInt($g('id_clase'),     self::MAX_SMALL);

        $id_generico  = $this->resolveGenericoId(
            $this->uInt($g('id_generico'), self::MAX_SMALL),
            $g('generico_nombre','generico','nombre_generico')
        );

        $estado = $this->normEstado((string) $g('estado'));

        return new Productos([
            'codigo' => $codigo,
            'descripcion' => $descripcion,
            'presentacion' => $presentacion,
            'laboratorio' => $laboratorio,
            'lote' => $lote,
            'fecha_vencimiento' => $fecha_vencimiento,

            'unidades_por_blister' => $u_blister,
            'unidades_por_caja'    => $u_caja,
            'cantidad'             => $cantidad,
            'cantidad_blister'     => $cantidad_blister,
            'cantidad_caja'        => $cantidad_caja,
            'stock_minimo'         => $stock_min,
            'stock_minimo_blister' => $stock_min_blister,
            'stock_minimo_caja'    => $stock_min_caja,

            'descuento'             => $desc_uni,
            'descuento_blister'     => $desc_blister,
            'descuento_caja'        => $desc_caja,
            'precio_compra'         => $pc_uni,
            'precio_compra_blister' => $pc_blister,
            'precio_compra_caja'    => $pc_caja,
            'precio_venta'          => $pv_uni,
            'precio_venta_blister'  => $pv_blister,
            'precio_venta_caja'     => $pv_caja,

            'foto'         => $foto ?: null,
            'id_proveedor' => $id_proveedor ?: null,
            'id_clase'     => $id_clase ?: null,
            'id_generico'  => $id_generico ?: null,
            'estado'       => $estado,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.codigo'        => ['required','string','max:255'],
            '*.descripcion'   => ['required','string'],
            '*.presentacion'  => ['required','string','max:255'], // requerido porque tu BD es NOT NULL
            '*.precio_venta'  => ['required'],                    // alerta si falta en el Excel
        ];
    }

    // ===== Helpers =====

    private function firstNonEmpty(array $row, array|string $keys): mixed
    {
        foreach ((array)$keys as $k) {
            if (array_key_exists($k, $row) && $row[$k] !== null && $row[$k] !== '') return $row[$k];
        }
        return null;
    }

    /** Trata "NULL", "N/A", "-", vacío como null (NO incluye "SIN PRESENTACION") */
    private function nullIfNullish($v): ?string
    {
        if ($v === null) return null;
        $s = trim((string)$v);
        if ($s === '' ) return null;
        $upper = mb_strtoupper($s);
        if (in_array($upper, ['NULL','N/A','NA','-','NINGUNO','NONE'], true)) return null;
        return $s;
    }

    /** Normaliza la presentación. Nunca retorna null; usa "SIN PRESENTACION" como fallback. */
    private function normPresentacion($v): string
    {
        if ($v === null) return 'SIN PRESENTACION';
        $s = trim((string)$v);
        if ($s === '') return 'SIN PRESENTACION';

        $u = mb_strtoupper($s);
        if (in_array($u, ['NULL','N/A','NA','-','NINGUNO','NONE'], true)) {
            return 'SIN PRESENTACION';
        }
        if ($u === 'SIN PRESENTACIÓN') return 'SIN PRESENTACION';
        return $s;
    }

    /** Convierte a entero no negativo y recorta al máximo permitido */
    private function uInt($v, int $max): int
    {
        if ($v === null || $v === '') return 0;
        if (is_string($v) && preg_match('/^\s*null\s*$/i', $v)) return 0;

        // Mantén solo dígitos (quita separadores/decimales)
        $s = preg_replace('/\D+/', '', (string)$v);
        if ($s === '') return 0;

        $n = (int)$s;
        if ($n < 0)    $n = 0;
        if ($n > $max) $n = $max;
        return $n;
    }

    private function toDecimal($v): ?float
    {
        if ($v === null || $v === '') return null;
        if (is_string($v) && preg_match('/^\s*null\s*$/i', $v)) return null;

        $s = trim((string)$v);
        $s = str_replace([' ', "\u{00A0}"], '', $s);

        // Formato con coma decimal (1.234,56)
        if (preg_match('/,\d{1,4}$/', $s)) {
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
        } else {
            // Punto decimal; quitar comas de miles
            $s = str_replace(',', '', $s);
        }
        return is_numeric($s) ? (float)$s : null;
    }

    private function parseFecha(?string $v): ?Carbon
    {
        if (!$v) return null;
        $v = trim($v);

        // Serial de Excel
        if (is_numeric($v)) {
            try {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($v));
            } catch (\Throwable $e) {}
        }
        foreach (['d/m/Y','Y-m-d','m/d/Y','d-m-Y','m-d-Y'] as $fmt) {
            try { $d = Carbon::createFromFormat($fmt, $v); if ($d!==false) return $d; } catch (\Throwable $e) {}
        }
        try { return Carbon::parse($v); } catch (\Throwable $e) { return null; }
    }

    private function normEstado(?string $raw): string
    {
        $s = mb_strtoupper(trim((string)$raw));
        $s = preg_replace('/\s+/', ' ', $s);
        $inactivo = ['INACTIVO','I','0','NO','DESHABILITADO','NO DISPONIBLE','FALSE','OFF','N','DESACTIVADO','BAJA'];
        $activo   = ['ACTIVO','A','1','SI','SÍ','TRUE','ON','DISPONIBLE','HABILITADO','ALTA'];
        if ($s === '') return 'Activo';
        if (in_array($s, $inactivo, true)) return 'Inactivo';
        if (in_array($s, $activo, true))   return 'Activo';
        if (preg_match('/^INAC|^IN-/', $s)) return 'Inactivo';
        return 'Activo';
    }

    private function resolveGenericoId(?int $id, $nombrePosible): ?int
    {
        if ($id) return $id;
        $nombre = trim((string)$nombrePosible);
        if ($nombre === '') return null;

        $key = mb_strtolower($nombre);
        if (isset($this->cacheGenericos[$key])) return $this->cacheGenericos[$key];

        $g = Genericos::query()->select('id')->where('nombre', $nombre)->first();
        $this->cacheGenericos[$key] = $g?->id ?? null;
        return $this->cacheGenericos[$key];
    }

    /**
     * Busca el primer valor cuyo encabezado CONTENGA todos los fragmentos dados (case-insensitive),
     * ignorando guiones, guiones bajos y espacios normales/duros (NBSP).
     * Útil para headers “sucios”: "PRECIO VENTA ", "precio-venta", "precio_venta__".
     */
    private function firstByContains(array $row, array $fragments): ?string
    {
        $norm = function ($k) {
            $k = str_replace(["\u{00A0}"], ' ', (string)$k); // NBSP -> espacio
            $k = trim(mb_strtolower($k));
            $k = preg_replace('/[\s\-_]+/u', ' ', $k);       // unificar separadores
            return $k;
        };
        $frags = array_map(fn($f) => $norm($f), $fragments);
        foreach ($row as $key => $val) {
            $nk = $norm($key);
            $ok = true;
            foreach ($frags as $f) { if (mb_strpos($nk, $f) === false) { $ok = false; break; } }
            if ($ok) return $val;
        }
        return null;
    }
}
