<?php

require_once("config.php");


//diretorios
$dir = "diretorio";

if(!is_dir($dir)){ //se NAO existe o diretorio
    mkdir($dir);  // cira o diretorio
    echo "criou diretorio $dir <br>";
} else {  //se existe
    //rmdir($name);  // exclui o diretorio, se vazio
    echo "excluiu diretorio $dir <br>";
}

//ver arquivos
$arquivos = scandir($dir);
foreach ($arquivos as $key => $file){
    if(!in_array($file, array('.','..'))){ //busca no array arquivos e se NAO for . ou .. faz ação
        $filename = $dir.DIRECTORY_SEPARATOR.$file; //caminho completo do arquivo
        $info = pathinfo($filename); //obtem informações do arquivo (diretorio, nome extenção)
        $info['size'] = filesize($filename);  //obtem tammanho em bytes do arquivo
        $info['modified'] = date("d/m/Y H:i:s",filemtime($filename)); //retoran data de modificaçao do arquivo
        $info['url'] = "http://localhost/sites/curso_php".str_replace("\\","/",$filename); //obtem url para download do arquivo (começo do link alterar)

    }
}

//manipular arquivo

$file = fopen("log.txt","a+"); //abre o arquivo com a permissao definida 
//(w para escrita, + para criar caso nao haja o arquivo - coloca no começo e exclui o que tinha antes)
//a+(a para escrita no final, + para criar caso nao haja o arquivo)

fwrite($file, date("Y-m-d H:i:s")."\r\n"); //escreve no arquivo $file a data. \r\n para nova linha

fclose($file); //fecha o arquivo tirando da memoria.
echo "arquivo criado, escrito e fechado <br>";


//criar CSV com dados do banco

$sql = new Sql();

$users = $sql->select("SELECT * FROM users ORDER BY login;");

$headers = array();

foreach($users[0] as $key => $value){ //percorre o primeiro usuario coluna por coluna
    array_push($headers, ucfirst($key)); // pega a chave de cada coluna e salva no array de cabeçalho, deixando primeira letra  maiuscula
}
$delimiter = " , ";
$linhaCabecalho = implode($delimiter,$headers)."\r\n"; //quebra o array em uma string separada por " , " (espaço virgula espaço)

$file = fopen("users.csv", "w+"); //abre o arquivo ou cria escrevendo do começo

fwrite($file,$linhaCabecalho);


foreach($users as $key => $row){
    $dadosUsuario = array();
    foreach ($row as $k => $value ){
        array_push($dadosUsuario, $value); //salva cada coluna do usuario no array dados.
    }
    $linhaDado = implode($delimiter,$dadosUsuario)."\r\n";
    fwrite($file, $linhaDado);
}

fclose($file);

$filename = "teste.txt";

//deletar arquivos e diretorios com arquivos
$file = fopen($filename, "w+");
fclose($file);

unlink($filename);  //apaga o arquivo descrito
//para excluir todos arquivos de uma pasta usar a funçao ja descrita acima que 
//percorre todo o diretorio, dando unlink dos arquivos lidos.


//lendo um arquivo
$filename = "users.csv";

if (file_exists($filename)){ //se o arquivo existir....
    $file = fopen($filename, "r"); // r serve para abrir e ler o arquivo, sem o + pois sabe-se que há o arquivo.

    $headers = fgets($file); //le a primeira linha do arquivo.
    $headers = explode($delimiter, str_replace("\r\n","",$headers)); //transforma a linha lida em um array.

    $conteudo = array();

    while($row = fgets($file)){ //se ainda houver linhas para ler, fará isso.

        $dado = explode($delimiter, str_replace("\r\n","",$row)); //transforma a linha num array
        $linha = array(); //cria um array para ser o array inserido no array de conteudo

        for ($i = 0; $i < count($headers); $i++){ //percorre de 0 até o tamnaho do cabeçalho para criar um array com indice personalizado
            $linha[$headers[$i]] = $dado[$i]; //pega o valor textual de header na posicao $i para ser o indice do dado especifico de posicao $i e salva no array 
        }
        
        array_push($conteudo, $linha); //salva no array de conteudo a linha com indice personalizado

    }

    fclose($file);

    echo json_encode ($conteudo); //imprime na tela um json do array.

}


//le todo arquivo binario.

$filename = "imagem.png";

$base64 = base64_encode(file_get_contents($filename)); //transforma em string com base64 todo conteudo lido do arquivo

$fileinfo = new finfo(FILEINFO_MIME_TYPE); //pega o tipo do arquivo.

$mimetype = $fileinfo->file($filename); //salva o formato do arquivo 

$base63encode = "data:".$mimetype.";base64,".$base64;

?>

<a href="<?=$base64encode?>" target="_blank">link arquivo base64</a> <!-- <?=$base64encode?> faz echo da variavel

<?php

//..

?>

