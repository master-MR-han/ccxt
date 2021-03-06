<?php

namespace ccxt;

// PLEASE DO NOT EDIT THIS FILE, IT IS GENERATED AND WILL BE OVERWRITTEN:
// https://github.com/ccxt/ccxt/blob/master/CONTRIBUTING.md#how-to-contribute-code

use Exception as Exception; // a common import

class mixcoins extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'mixcoins',
            'name' => 'MixCoins',
            'countries' => array ( 'GB', 'HK' ),
            'rateLimit' => 1500,
            'version' => 'v1',
            'has' => array (
                'CORS' => false,
            ),
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/30237212-ed29303c-9535-11e7-8af8-fcd381cfa20c.jpg',
                'api' => 'https://mixcoins.com/api',
                'www' => 'https://mixcoins.com',
                'doc' => 'https://mixcoins.com/help/api/',
            ),
            'api' => array (
                'public' => array (
                    'get' => array (
                        'ticker',
                        'trades',
                        'depth',
                    ),
                ),
                'private' => array (
                    'post' => array (
                        'cancel',
                        'info',
                        'orders',
                        'order',
                        'transactions',
                        'trade',
                    ),
                ),
            ),
            'markets' => array (
                'BTC/USD' => array( 'id' => 'btc_usd', 'symbol' => 'BTC/USD', 'base' => 'BTC', 'quote' => 'USD', 'baseId' => 'btc', 'quoteId' => 'usd', 'maker' => 0.0015, 'taker' => 0.0025 ),
                'ETH/BTC' => array( 'id' => 'eth_btc', 'symbol' => 'ETH/BTC', 'base' => 'ETH', 'quote' => 'BTC', 'baseId' => 'eth', 'quoteId' => 'btc', 'maker' => 0.001, 'taker' => 0.0015 ),
                'BCH/BTC' => array( 'id' => 'bch_btc', 'symbol' => 'BCH/BTC', 'base' => 'BCH', 'quote' => 'BTC', 'baseId' => 'bch', 'quoteId' => 'btc', 'maker' => 0.001, 'taker' => 0.0015 ),
                'LSK/BTC' => array( 'id' => 'lsk_btc', 'symbol' => 'LSK/BTC', 'base' => 'LSK', 'quote' => 'BTC', 'baseId' => 'lsk', 'quoteId' => 'btc', 'maker' => 0.0015, 'taker' => 0.0025 ),
                'BCH/USD' => array( 'id' => 'bch_usd', 'symbol' => 'BCH/USD', 'base' => 'BCH', 'quote' => 'USD', 'baseId' => 'bch', 'quoteId' => 'usd', 'maker' => 0.001, 'taker' => 0.0015 ),
                'ETH/USD' => array( 'id' => 'eth_usd', 'symbol' => 'ETH/USD', 'base' => 'ETH', 'quote' => 'USD', 'baseId' => 'eth', 'quoteId' => 'usd', 'maker' => 0.001, 'taker' => 0.0015 ),
            ),
        ));
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostInfo ($params);
        $balance = $this->safe_value($response['result'], 'wallet');
        $result = array( 'info' => $balance );
        $currencies = is_array($this->currencies) ? array_keys($this->currencies) : array();
        for ($i = 0; $i < count ($currencies); $i++) {
            $code = $currencies[$i];
            $currencyId = $this->currencyid ($code);
            if (is_array($balance) && array_key_exists($currencyId, $balance)) {
                $account = $this->account ();
                $account['free'] = $this->safe_float($balance[$currencyId], 'avail');
                $account['used'] = $this->safe_float($balance[$currencyId], 'lock');
                $account['total'] = $this->sum ($account['free'], $account['used']);
                $result[$code] = $account;
            }
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'market' => $this->market_id($symbol),
        );
        $response = $this->publicGetDepth (array_merge ($request, $params));
        return $this->parse_order_book($response['result']);
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $request = array (
            'market' => $this->market_id($symbol),
        );
        $response = $this->publicGetTicker (array_merge ($request, $params));
        $ticker = $this->safe_value($response, 'result');
        $timestamp = $this->milliseconds ();
        $last = $this->safe_float($ticker, 'last');
        return array (
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
            'bid' => $this->safe_float($ticker, 'buy'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'sell'),
            'askVolume' => null,
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, 'vol'),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function parse_trade ($trade, $market) {
        $timestamp = intval ($trade['date']) * 1000;
        return array (
            'id' => (string) $trade['id'],
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $market['symbol'],
            'type' => null,
            'side' => null,
            'price' => $this->safe_float($trade, 'price'),
            'amount' => $this->safe_float($trade, 'amount'),
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'market' => $market['id'],
        );
        $response = $this->publicGetTrades (array_merge ($request, $params));
        return $this->parse_trades($response['result'], $market, $since, $limit);
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'market' => $this->market_id($symbol),
            'op' => $side,
            'amount' => $amount,
        );
        if ($type === 'market') {
            $request['order_type'] = 1;
            $request['price'] = $price;
        } else {
            $request['order_type'] = 0;
        }
        $response = $this->privatePostTrade (array_merge ($request, $params));
        return array (
            'info' => $response,
            'id' => (string) $response['result']['id'],
        );
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'id' => $id,
        );
        return $this->privatePostCancel (array_merge ($request, $params));
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $this->version . '/' . $path;
        if ($api === 'public') {
            if ($params) {
                $url .= '?' . $this->urlencode ($params);
            }
        } else {
            $this->check_required_credentials();
            $nonce = $this->nonce ();
            $body = $this->urlencode (array_merge (array (
                'nonce' => $nonce,
            ), $params));
            $headers = array (
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Key' => $this->apiKey,
                'Sign' => $this->hmac ($this->encode ($body), $this->secret, 'sha512'),
            );
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function request ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $response = $this->fetch2 ($path, $api, $method, $params, $headers, $body);
        if (is_array($response) && array_key_exists('status', $response)) {
            if ($response['status'] === 200) {
                return $response;
            }
        }
        throw new ExchangeError($this->id . ' ' . $this->json ($response));
    }
}
