<?php

class Des
{

    //密钥
    protected $key;

    /*
     * $key 默认密钥
     */
    function __construct($key="qin500hh")
    {
        $this->key = $key;
    }

    function encryption($plaintext)
    {


        // 补充密钥到8字节(64位)
        $key = str_pad($this->key, 8, "\0");

        // 设置加密算法和参数
        $algorithm = "des-ecb";
        $options = OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING;

        // 填充明文
        $paddingLength = 8 - strlen($plaintext) % 8;
        $plaintext .= str_repeat(chr($paddingLength), $paddingLength);

        // 执行加密操作
        $ciphertext = openssl_encrypt($plaintext, $algorithm, $key, $options);

        // 将密文转为十六进制字符串
        $ciphertextHex = bin2hex($ciphertext);

        return $ciphertextHex;
    }

    function decryption($ciphertextHex)
    {
        // 将密文从十六进制字符串转换为二进制数据
        $ciphertext = hex2bin($ciphertextHex);

        // 补充密钥到8字节（64位）
        $key = str_pad($this->key, 8, "\0");


        // 设置解密算法和参数
        $algorithm = "des-ecb";
        $options = OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING;


        // 执行解密操作
        $plaintext = openssl_decrypt($ciphertext, $algorithm, $key, $options);

        // 移除填充字节
        $paddingLength = ord($plaintext[strlen($plaintext) - 1]);
        $plaintext = substr($plaintext, 0, -$paddingLength);

        return $plaintext;
    }

}




if (strtoupper($_SERVER['REQUEST_METHOD']) == "POST") {
    $text=$_POST['text'];
    $des=new Des;
    if($_POST['type'] === "encry"){
       $res= $des->encryption($text);
    }else{
        $res=$des->decryption($text);
    }

    echo $res;


    exit();
}


?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DES加解密</title>
    <style>
        #res{
            padding: 20px;
            background: #f4cc48;
            color: #342a00;
            margin: 20px 0;
            box-sizing: border-box;
            width: 100%;
            outline: none;
            font-size: 18px;
            resize: vertical;
        }
    </style>
</head>
<body>

<form method="post" enctype="application/x-www-form-urlencoded">
    <fieldset>
        <legend>加密:</legend>
        <input type="hidden" name="type" value="encry">
        文本: <input type="text" name="text"><br>
        <input type="submit" value="加密">
    </fieldset>
</form>
<form method="post" enctype="application/x-www-form-urlencoded">
    <fieldset>
        <legend>解密:</legend>
        <input type="hidden" name="type" value="decry">
        文本: <input type="text" name="text"><br>
        <input type="submit" value="解密">
    </fieldset>
</form>
<textarea id="res"></textarea>


<script>
    let form = document.forms;
    Array.from(form).forEach(function (item, index, array) {
        item.addEventListener('submit', function (e) {


            // // 获取表单数据
            let data = {};
            let elements =item.elements
            for (let element of elements) {
                data[element.name] = element.value;
            }

            let text=item.querySelector("[name='text']");
            let type=item.querySelector("[name='type']");
            let res_v=document.querySelector("#res");

            fetch("",{
                method:'post',
                headers:{
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body:`text=${text.value}&type=${type.value}`
            }).then(res=>{
               return res.text()
            }).then(r=>{
                console.log(r)
                res_v.textContent=r
            })

            e.preventDefault()
        })
    })


</script>
</body>
</html>

