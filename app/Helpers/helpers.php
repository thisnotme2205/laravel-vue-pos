<?php
    function idrCurrency($value){
        return 'Rp. '.number_format($value, 0, ",", ".");
    }

    function dateFormat($value){
        return date('d M Y', strtotime($value));
    }