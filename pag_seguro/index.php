<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Document</title>
</head>
<body>
<br>
<br>
<section class="pagamento">
    <div class="container">
        <h2>Efetuar pagamento</h2>
        <br>
        <form>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="nome" class="form-label">Nome Completo:</label>
                        <input name="nome" type="text" class="form-control" id="nome" placeholder="Insira seu nome">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="cpf" class="form-label">CPF:</label>
                        <input name="cpf" type="text" class="form-control" id="cpf" placeholder="Seu cpf">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="bandeira" class="form-label">Bandeiras:</label>
                        <select class="form-control" name="bandeira">
                           
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nome" class="form-label">Valor:</label>
                        <select class="form-control" name="valores">

                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="numero_cartao" class="form-label">Número do cartão:</label>
                        <input name="numero_cartao" type="text" class="form-control" placeholder="Número do cartão">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Mês de validade:</label>
                        <input name="mes_validade" type="text" class="form-control" placeholder="Mês de validade">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Ano de validade::</label>
                        <input name="ano_validade" type="text" class="form-control" placeholder="Ano validade">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="numero_cartao" class="form-label">CVV:</label>
                        <input name="cvv" type="text" class="form-control" placeholder="CVV do cartão">
                    </div>
                </div>
                <div class="col-md-12">
                    <button name="acao" type="submit" class="btn btn-primary">Enviar!</button>
                </div>
            </div>
        </form>
    </div><!--container-->
</section>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

	<script src="https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js"></script>

<script type="text/javascript">
    valor = 400.50;
    var imagens = [];

    //Listar bandeiras

    $.ajax({
        dataType:'json',
        url:'cartao_credito.php',
        method:'post',
        data:{'gerar_sessao':'true'}
    }).done(function(data){
        PagSeguroDirectPayment.setSessionId(data.id);
        PagSeguroDirectPayment.getPaymentMethods({

            success: function(response){
                var bancos = '';
                var bandeiras = '';

                $.each(response.paymentMethods.CREDIT_CARD.options,function(key,value){
                    imagens[value.name.toLowerCase()] = 'https://stc.pagseguro.uol.com.br'+value.images.MEDIUM.path;
                    bandeiras+='<option value="'+value.name.toLowerCase()+'">'+value.name+'</option>';
                })
                $('select[name=bandeira]').html(bandeiras);
            }

        });
    })

    //DETECTANDO A BANDEIRA DO CARTÂO.

    $('input[name=numero_cartao]').on('keyup',function(){
        if($(this).val().length >= 6){
            PagSeguroDirectPayment.getBrand({
                cardBin:$(this).val().substring(0,6),
                success:function(v){
                    var cartao = v.brand.name;

                    PagSeguroDirectPayment.getInstallments({
                        amount:valor,
                        maxInstallmentNoInterest:4,
                        brand:cartao,
                        success:function(data){
                            var bandeirasSelect = $('select[name=bandeira]');
                            bandeirasSelect.find('option').removeAttr('selected');
                            bandeirasSelect.find('option[value='+cartao+']').attr('selected','selected');

                            //Listar opções de parcelamento.
                            $('select[name=valores]').html('');
                            $.each(data.installments[cartao],function(index,value){
                                var htmAtual = $('select[name=valores]').html();
                                var valorParcela = value.installmentAmount;
                                var juros = value.interestFree == true ? ' sem juros ' : ' com juros '; 
                                $('select[name=valores]').html(htmAtual+'<option value="'+(index+1)+':'+valorParcela+'">'+valorParcela+juros+'</option>');
                            })

                        }
                    })
                }
            })
        }
    });

    $('select[name=bandeira]').change(function(){
        var bandeira = $(this).val();
        PagSeguroDirectPayment.getInstallments({
                        amount:valor,
                        maxInstallmentNoInterest:4,
                        brand:bandeira,
                        success:function(data){
                            //Listar opções de parcelamento.
                            $('select[name=valores]').html('');
                            $.each(data.installments[bandeira],function(index,value){
                                
                                var htmAtual = $('select[name=valores]').html();
                                var valorParcela = value.installmentAmount;
                                var juros = value.interestFree == true ? ' sem juros ' : ' com juros '; 
                                $('select[name=valores]').html(htmAtual+'<option value="'+(index+1)+':'+valorParcela+'">'+valorParcela+juros+'</option>');
                            })

                        }
                    })
    });
    
    //Formulário PRINCIPAL.

    $('form').submit(function(e){
        e.preventDefault();
        disableForm();

        var numero_cartao = $('[name=numero_cartao]').val();
        var cvv = $('[name=cvv]').val();
        var bandeira = $('[name=bandeira]').val();
        var parcela = $('[name=valores]').val();
        var mes = $('[name=mes_validade]').val();
        var ano = $('[name=ano_validade]').val();

        var hash = PagSeguroDirectPayment.getSenderHash();

        //pegar a bandeira
        // var bin = numero_cartao.substring(0,6);
        PagSeguroDirectPayment.createCardToken({
            cardNumber: numero_cartao,
            brand:bandeira,
            cvv:cvv,
            expirationMonth:mes,
            expirationYear:ano,
            success:function(data){
                var token = data.card.token;
                var splitParcelas = parcela.split(':');
                var valorParcela = splitParcelas[1];
                var numeroParcela = splitParcelas[0];
                $.ajax({
                    'method':'post',
                    'dataType':'json',
                    'url':'cartao_credito.php',
                    'data':{'fechar_pedido':true,'token':token,'cartao':bandeira,'parcelas':numeroParcela,'valorParcela':valorParcela,
                    'hash':hash,'amount':valor},
                    success:function(data){
                        if(data.status == undefined){
                            alert('ocorreu um erro ao pagar!');
                        }else{
                            enableForm();
                            alert('Pagemento foi efetuado com sucesso!');
                        }
                    }

                })
            },
            error:function(data){
                alert('ocorreu algum erro ao pagar');
            }
        })

    })

    function disableForm(){
        $('form').animate({'opcity':'0.4'});
        $('form').find('input').attr('disabled','disabled');
        $('form').find('button').attr('disabled','disabled');
        $('form').find('select').attr('disabled','disabled');
    }

    function enableForm(){
        $('form').animate({'opcity':'1'});
        $('form').find('input').removeAttr('disabled');
        $('form').find('button').removeAttr('disabled');
        $('form').find('select').removeAttr('disabled');
    }

</script>
</body>
</html>