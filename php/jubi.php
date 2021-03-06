<?php

namespace ccxt;

// PLEASE DO NOT EDIT THIS FILE, IT IS GENERATED AND WILL BE OVERWRITTEN:
// https://github.com/ccxt/ccxt/blob/master/CONTRIBUTING.md#how-to-contribute-code

use Exception as Exception; // a common import

class jubi extends btcbox {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'jubi',
            'name' => 'jubi.com',
            'countries' => array ( 'CN' ),
            'rateLimit' => 1500,
            'version' => 'v1',
            'has' => array (
                'CORS' => false,
                'fetchTickers' => true,
            ),
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/27766581-9d397d9a-5edd-11e7-8fb9-5d8236c0e692.jpg',
                'api' => 'https://www.jubi.com/api',
                'www' => 'https://www.jubi.com',
                'doc' => 'https://www.jubi.com/help/api.html',
            ),
        ));
    }

    public function fetch_markets ($params = array ()) {
        $markets = $this->publicGetAllticker ($params);
        $keys = is_array($markets) ? array_keys($markets) : array();
        $result = array();
        for ($i = 0; $i < count ($keys); $i++) {
            $id = $keys[$i];
            $baseId = $id;
            $quoteId = 'cny';
            $base = $this->common_currency_code(strtoupper($baseId));
            $quote = $this->common_currency_code(strtoupper($quoteId));
            $symbol = $base . '/' . $quote;
            $result[] = array (
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'info' => $id,
            );
        }
        return $result;
    }
}
