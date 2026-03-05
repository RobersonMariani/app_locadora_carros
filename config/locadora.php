<?php

declare(strict_types=1);

return [
    'multa_atraso_percentual' => (float) env('LOCADORA_MULTA_ATRASO_PERCENTUAL', 10),
    'custo_km_extra' => (float) env('LOCADORA_CUSTO_KM_EXTRA', 1.5),
    'km_livre_por_dia' => (int) env('LOCADORA_KM_LIVRE_POR_DIA', 100),
];
