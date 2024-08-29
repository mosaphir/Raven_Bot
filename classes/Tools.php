<?php

    class Tools {
        #------[ARRAY RAND]------#
        static public function RanArray($array){
            $arr = $array[array_rand($array)];
            return $arr;
            
        }

        #------[Get Tokens]------#
        static public function GetToken($p,$l,$t) {
            return explode($t,explode($l,$p)[1])[0];
        }


        #------[Delete Spaces]------#
        static public function DeleteSpaces($input) {
            $result = preg_replace('/\s\s+/', ' ', $input);
            if ($result[0] == ' ') {
                $result = substr($result, 1);
            } $quir = substr($result, -1);
            if ($quir == ' ') {
                $result = substr($result, 0, -1);
            }
            return $result;
        }

        #------[Multi Explode]------#
        static public function multiexplode($delimiters, $string) {
            $ready = str_replace($delimiters, $delimiters[0], $string);
            $launch = explode($delimiters[0], $ready);
            return  $launch;
        }

        #------[Get Flag]------#
        static public function GetFlag($code) {
            $flags = ['AD' => '🇦🇩', 'AE' => '🇦🇪', 'AF' => '🇦🇫', 'AG' => '🇦🇬', 'AI' => '🇦🇮', 'AL' => '🇦🇱', 'AM' => '🇦🇲', 'AO' => '🇦🇴', 'AQ' => '🇦🇶', 'AR' => '🇦🇷', 'AS' => '🇦🇸', 'AT' => '🇦🇹', 'AU' => '🇦🇺', 'AW' => '🇦🇼', 'AX' => '🇦🇽', 'AZ' => '🇦🇿', 'BA' => '🇧🇦', 'BB' => '🇧🇧', 'BD' => '🇧🇩', 'BE' => '🇧🇪', 'BG' => '🇧🇬', 'BH' => '🇧🇭', 'BI' => '🇧🇮', 'BJ' => '🇧🇯', 'BL' => '🇧🇱', 'BF' => '🇧🇫', 'BM' => '🇧🇲', 'BN' => '🇧🇳', 'BO' => '🇧🇴', 'BQ' => '🇧🇶', 'BR' => '🇧🇷', 'BS' => '🇧🇸', 'BT' => '🇧🇹', 'BV' => '🇧🇻', 'BW' => '🇧🇼', 'BY' => '🇧🇾', 'BZ' => '🇧🇿', 'CA' => '🇨🇦', 'CC' => '🇨🇨', 'CD' => '🇨🇩', 'CF' => '🇨🇫', 'CG' => '🇨🇬', 'CH' => '🇨🇭', 'CI' => '🇨🇮', 'CK' => '🇨🇰', 'CL' => '🇨🇱', 'CM' => '🇨🇲', 'CN' => '🇨🇳', 'CO' => '🇨🇴', 'CR' => '🇨🇷', 'CU' => '🇨🇺', 'CV' => '🇨🇻', 'CW' => '🇨🇼', 'CX' => '🇨🇽', 'CY' => '🇨🇾', 'CZ' => '🇨🇿', 'DE' => '🇩🇪', 'DJ' => '🇩🇯', 'DK' => '🇩🇰', 'DM' => '🇩🇲', 'DO' => '🇩🇴', 'DZ' => '🇩🇿', 'EC' => '🇪🇨', 'EE' => '🇪🇪', 'EG' => '🇪🇬', 'EH' => '🇪🇭', 'ER' => '🇪🇷', 'ES' => '🇪🇸', 'ET' => '🇪🇹', 'FI' => '🇫🇮', 'FJ' => '🇫🇯', 'FK' => '🇫🇰', 'FM' => '🇫🇲', 'FO' => '🇫🇴', 'FR' => '🇫🇷', 'GA' => '🇬🇦', 'GB' => '🇬🇧', 'GD' => '🇬🇩', 'GE' => '🇬🇪', 'GF' => '🇬🇫', 'GG' => '🇬🇬', 'GH' => '🇬🇭', 'GI' => '🇬🇮', 'GL' => '🇬🇱', 'GM' => '🇬🇲', 'GN' => '🇬🇳', 'GP' => '🇬🇵', 'GQ' => '🇬🇶', 'GR' => '🇬🇷', 'GS' => '🇬🇸', 'GT' => '🇬🇹', 'GU' => '🇬🇺', 'GW' => '🇬🇼', 'GY' => '🇬🇾', 'HK' => '🇭🇰', 'HM' => '🇭🇲', 'HN' => '🇭🇳', 'HR' => '🇭🇷', 'HT' => '🇭🇹', 'HU' => '🇭🇺', 'ID' => '🇮🇩', 'IE' => '🇮🇪', 'IL' => '🇮🇱', 'IM' => '🇮🇲', 'IN' => '🇮🇳', 'IO' => '🇮🇴', 'IQ' => '🇮🇶', 'IR' => '🇮🇷', 'IS' => '🇮🇸', 'IT' => '🇮🇹', 'JE' => '🇯🇪', 'JM' => '🇯🇲', 'JO' => '🇯🇴', 'JP' => '🇯🇵', 'KE' => '🇰🇪', 'KG' => '🇰🇬', 'KH' => '🇰🇭', 'KI' => '🇰🇮', 'KM' => '🇰🇲', 'KN' => '🇰🇳', 'KP' => '🇰🇵', 'KR' => '🇰🇷', 'KW' => '🇰🇼', 'KY' => '🇰🇾', 'KZ' => '🇰🇿', 'LA' => '🇱🇦', 'LB' => '🇱🇧', 'LC' => '🇱🇨', 'LI' => '🇱🇮', 'LK' => '🇱🇰', 'LR' => '🇱🇷', 'LS' => '🇱🇸', 'LT' => '🇱🇹', 'LU' => '🇱🇺', 'LV' => '🇱🇻', 'LY' => '🇱🇾', 'MA' => '🇲🇦', 'MC' => '🇲🇨', 'MD' => '🇲🇩', 'ME' => '🇲🇪', 'MF' => '🇲🇫', 'MG' => '🇲🇬', 'MH' => '🇲🇭', 'MK' => '🇲🇰', 'ML' => '🇲🇱', 'MM' => '🇲🇲', 'MN' => '🇲🇳', 'MO' => '🇲🇴', 'MP' => '🇲🇵', 'MQ' => '🇲🇶', 'MR' => '🇲🇷', 'MS' => '🇲🇸', 'MT' => '🇲🇹', 'MU' => '🇲🇺', 'MV' => '🇲🇻', 'MW' => '🇲🇼', 'MX' => '🇲🇽', 'MY' => '🇲🇾', 'MZ' => '🇲🇿', 'NA' => '🇳🇦', 'NC' => '🇳🇨', 'NE' => '🇳🇪', 'NF' => '🇳🇫', 'NG' => '🇳🇬', 'NI' => '🇳🇮', 'NL' => '🇳🇱', 'NO' => '🇳🇴', 'NP' => '🇳🇵', 'NR' => '🇳🇷', 'NU' => '🇳🇺', 'NZ' => '🇳🇿', 'OM' => '🇴🇲', 'PA' => '🇵🇦', 'PE' => '🇵🇪', 'PF' => '🇵🇫', 'PG' => '🇵🇬', 'PH' => '🇵🇭', 'PK' => '🇵🇰', 'PL' => '🇵🇱', 'PM' => '🇵🇲', 'PN' => '🇵🇳', 'PR' => '🇵🇷', 'PS' => '🇵🇸', 'PT' => '🇵🇹', 'PW' => '🇵🇼', 'PY' => '🇵🇾', 'QA' => '🇶🇦', 'RE' => '🇷🇪', 'RO' => '🇷🇴', 'RS' => '🇷🇸', 'RU' => '🇷🇺', 'RW' => '🇷🇼', 'SA' => '🇸🇦', 'SB' => '🇸🇧', 'SC' => '🇸🇨', 'SD' => '🇸🇩', 'SE' => '🇸🇪', 'SG' => '🇸🇬', 'SH' => '🇸🇭', 'SI' => '🇸🇮', 'SJ' => '🇸🇯', 'SK' => '🇸🇰', 'SL' => '🇸🇱', 'SM' => '🇸🇲', 'SN' => '🇸🇳', 'SO' => '🇸🇴', 'SR' => '🇸🇷', 'SS' => '🇸🇸', 'ST' => '🇸🇹', 'SV' => '🇸🇻', 'SX' => '🇸🇽', 'SY' => '🇸🇾', 'SZ' => '🇸🇿', 'TC' => '🇹🇨', 'TD' => '🇹🇩', 'TF' => '🇹🇫', 'TG' => '🇹🇬', 'TH' => '🇹🇭', 'TJ' => '🇹🇯', 'TK' => '🇹🇰', 'TL' => '🇹🇱', 'TM' => '🇹🇲', 'TN' => '🇹🇳', 'TO' => '🇹🇴', 'TR' => '🇹🇷', 'TT' => '🇹🇹', 'TV' => '🇹🇻', 'TW' => '🇹🇼', 'TZ' => '🇹🇿', 'UA' => '🇺🇦', 'UG' => '🇺🇬', 'UM' => '🇺🇲', 'US' => '🇺🇸', 'UY' => '🇺🇾', 'UZ' => '🇺🇿', 'VA' => '🇻🇦', 'VC' => '🇻🇨', 'VE' => '🇻🇪', 'VG' => '🇻🇬', 'VI' => '🇻🇮', 'VN' => '🇻🇳', 'VU' => '🇻🇺', 'WF' => '🇼🇫', 'WS' => '🇼🇸', 'XK' => '🇽🇰', 'YE' => '🇾🇪', 'YT' => '🇾🇹', 'ZA' => '🇿🇦', 'ZM' => '🇿🇲', 'ZW' => '🇿🇼'];
            return $flags[strtoupper($code)] ?? '◻️';
        }
        
        #------[Gen MUID, SUID, STRIPE]------#
        static private function GenStripeDatas() {
            $result = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
            $x = strtolower($result);
            $guid = substr($x, 0, 42);
            return $guid;
        }

        #------[Gen Password]------#
        static public function GenPass($lenght = 10) {
            $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
            $password = '';
            for ($i = 0; $i < $lenght; $i++) {
                $password .= substr($str, rand(0, 61), 1);
            }
            return $password;
        }

        #------[Get Random Users Data]------#
        static public function GetUser() {
            $addr = self::RanArray([
                ['NY', 'New York', '10080', 'New York'],
                ['WA', 'Washington', '98001', 'Auburn'],
                ['AL', 'Alabama', '35005', 'Adamsville'],
                ['FL', 'Florida', '32003', 'Orange Park'],
                ['CA', 'California', '90201', 'Bell']
            ]);

            $first = self::RanArray(['Johnathon', 'Anthony', 'Erasmo', 'Raleigh', 'Nancie', 'Tama', 'Camellia', 'Augustine', 'Christeen', 'Luz', 'Diego', 'Lyndia', 'Thomas', 'Georgianna', 'Leigha', 'Alejandro', 'Marquis', 'Joan', 'Stephania', 'Elroy', 'Zonia', 'Buffy', 'Sharie', 'Blythe', 'Gaylene', 'Elida', 'Randy', 'Margarete', 'Margarett', 'Dion', 'Tomi', 'Arden', 'Clora', 'Laine', 'Becki', 'Margherita', 'Bong', 'Jeanice', 'Qiana', 'Lawanda', 'Rebecka', 'Maribel', 'Tami', 'Yuri', 'Michele', 'Rubi', 'Larisa', 'Lloyd', 'Tyisha', 'Samatha']);
            $last = self::RanArray(['Mischke', 'Serna', 'Pingree', 'Mcnaught', 'Pepper', 'Schildgen', 'Mongold', 'Wrona', 'Geddes', 'Lanz', 'Fetzer', 'Schroeder', 'Block', 'Mayoral', 'Fleishman', 'Roberie', 'Latson', 'Lupo', 'Motsinger', 'Drews', 'Coby', 'Redner', 'Culton', 'Howe', 'Stoval', 'Michaud', 'Mote', 'Menjivar', 'Wiers', 'Paris', 'Grisby', 'Noren', 'Damron', 'Kazmierczak', 'Haslett', 'Guillemette', 'Buresh', 'Center', 'Kucera', 'Catt', 'Badon', 'Grumbles', 'Antes', 'Byron', 'Volkman', 'Klemp', 'Pekar', 'Pecora', 'Schewe', 'Ramage']);

            return (object) [
                // Info
                'title' => self::RanArray(['Mr', 'Ms']),
                'first' => ucfirst($first),
                'last' => ucfirst($last),
                // Address
                'street' => self::RanArray(['Street '.rand(1, 232).' st', ''.rand(0000, 9999).' Main Street']),
                'city' => $addr[3],
                'state' => $addr[1],
                'state_code' => $addr[0],
                'zip' => $addr[2],
                // Contact
                'email' => ''.$first.''.$last.''.rand(10, 100).'@'.self::RanArray(['gmail.com', 'hotmail.com', 'outlook.com','aol.com']),
                'phone' => self::RanArray(["682", "346", "246"]).rand(0000000, 9999999),
                // Online
                'user' => ucfirst($first.'x'.rand(10, 1000)),
                'pass' => self::GenPass(),
                'ip' => rand(132, 255).'.'.rand(1,255).'.'.rand(1, 255).'.'.rand(10, 145),
                'guid' => self::GenStripeDatas(),
                'muid' => self::GenStripeDatas(),
                'sid' => self::GenStripeDatas()
            ];
        }

        static public function TypeCC($type) {
            
            $type = substr($type, 0, 1);

            if ($type == '3') {
                $type_cc = 'amex';
        
            } elseif ($type == '4') {
                $type_cc = 'visa';
        
            } elseif ($type == '5') {
                $type_cc = 'mastercard';
        
            } elseif ($type == '6') {
                $type_cc = 'discover';
            }

            return $type_cc;
        }

    }