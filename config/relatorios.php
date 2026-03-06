<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Custo horário médio (€/h)
    |--------------------------------------------------------------------------
    |
    | Valor usado nos relatórios para calcular o "custo baseado no tempo" e
    | comparar com o preço cobrado ao cliente (compensa ou não). Defina no
    | .env como RELATORIOS_CUSTO_HORA (ex: 25 para 25 €/h).
    |
    */

    'custo_hora_medio' => (float) env('RELATORIOS_CUSTO_HORA', 25),

];
