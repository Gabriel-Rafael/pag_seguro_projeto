<?php
    ini_set('max_execution_time','0');
    if(isset($_POST['gerar_sessao'])){
        $url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/sessions?email=gabrielrafa09@gmail.com&token=EDF22E40BE894F1CB22019FA7054DA46';
        $curl = curl_init($url);
        curl_setopt($curl,CURLOPT_POST,1);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);

        $retorno = curl_exec($curl);
        curl_close($curl);
        $session = simplexml_load_string($retorno);
        die(json_encode($session));
    }else if(isset($_POST['fechar_pedido'])){
        $data = [
            'email'=>'gabrielrafa09@gmail.com',
            'token'=>'EDF22E40BE894F1CB22019FA7054DA46',
            'paymentMode'=>'default',
            'paymentMethod'=>'creditCard',
            'receiverEmail'=>'gabrielrafa09@gmail.com',
            'currency'=>'BRL',
            'extraAmount'=>'0.00',
            'itemId1'=>'1',
            'itemDescription1'=>'camisa',
            'itemAmount1'=>number_format($_POST['amount'],2,'.',''),
            'itemQuantity1'=>'1',
            'notification'=>'https://localhost',
            'reference'=>uniqid(),
            'senderName'=>'Gabriel Rafael',
            'senderCPF'=>'09561166984',
            'senderAreaCode'=>'48',
            'senderPhone'=>'12345678',
            'senderEmail'=>'c11201520866819036390@sandbox.pagseguro.com.br',
            'senderHash'=>$_POST['hash'],
            'shippingAddressStreet'=>'Rua edson alexandre vieira',
            'shippingAddressNumber'=>'444',
            'shippingAddressComplement'=>'Casa 2',
            'shippingAddressDistrict'=>'centro',
            'shippingAddressPostalCode'=>'76990000',
            'shippingAddressCity'=>'seringeiras',
            'shippingAddressState'=>'RO',
            'shippingAddressCountry'=>'BRA',
            'shippingType'=>'3',
            'shippingCost'=>'0.00',
            'creditCardToken'=>$_POST['token'],
            'installmentQuantity'=>$_POST['parcelas'],
            'installmentValue'=>number_format($_POST['valorParcela'],2,'.',''),
            'noInterestInstallmentQuantity'=>4,
            'creditCardHolderName'=>'Gabriel Rafael',
            'creditCardHolderCPF'=>'09561166984',
            'creditCardHolderAreaCode'=>'48',
            'creditCardHolderPhone'=>'12345678',
            'billingAddressStreet'=>'Rua edson alexandre vieira',
            'billingAddressNumber'=>'244',
            'billingAddressComplement'=>'casa 1',
            'billingAddressDistrict'=>'Centro',
            'billingAddressPostalCode'=>'76990000',
            'billingAddressCity'=>'florianopolis',
            'billingAddressState'=>'RO',
            'billingAddressCountry'=>'BRA'
            ];
            $query = http_build_query($data);
            $url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/transactions';
            $curl = curl_init($url);
            curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/x-www-form-urlencoded;charset=UTF-8'));
            curl_setopt($curl,CURLOPT_POST,1);
            curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($curl,CURLOPT_POSTFIELDS,$query);

            $retorno = curl_exec($curl);
            $xml = json_encode(simplexml_load_string($retorno));

            die($xml);
    }
?>