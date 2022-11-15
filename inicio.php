<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda de contatos</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <?php
            //encontrar o banco de dados 
            try {
                $pdo = new PDO("mysql:dbname=agenda;host=localhost:3307","root","");
            } catch (PDOException $e) {
                echo "Erro com banco de dados: ".$e->getMessage();
            } catch (Exception $e){
                echo "Erro generico: ".$e->gerMessage();
            }

        //Pegar informacoes da pessoa quando clicar no botao 'Atualizar'
        if(isset($_GET['id_up'])){
            $info = $_GET['id_up'];
            $res = array();
            $cmd = $pdo->prepare("SELECT * FROM tb_pessoa WHERE id_pessoa = :id");
            $cmd->bindValue(":id",$info);
            $cmd->execute();
            $res = $cmd->fetch(PDO::FETCH_ASSOC);
            //print_r($res);
            //echo gettype($res["id_pessoa"]);
        }


    ?>

    <section id="formulario">
        <form action="" method = "post">
            <label for="name">Nome: </label>
            <input type="text" id="name" name="name" value="<?php if(isset($res)){echo $res['ds_nome'];} ?>" required>
            <br>
            <label for="">Genero: </label>
            <input type="radio" id="m" name="gender" value="M" <?php if(isset($res)){if($res['cd_sexo'] === 'M'){echo "checked";} } ?> >
            <label for="m">Masculino</label> 
            <input type="radio" id="f" name="gender" value="F" <?php if(isset($res)){if($res['cd_sexo'] === 'F'){echo "checked";} }  ?> >
            <label for="f">Feminino</label>
            <input type="radio" id="n" name="gender" value="N" <?php if(isset($res)){if($res['cd_sexo'] === 'N'){echo "checked";} }  ?> >
            <label for="n">Prefiro nao opinar</label>
            <br>
            <label for="b_date">Data de Nascimento: </label>
            <input type="date" id="b_date" name="b_date" value=<?php if(isset($res)){echo $res['dt_nasc'];} ?> required>
            <br>
            <label for="number">Telefone: </label>
            <input type="text" id="number" name="number" value="<?php if(isset($res)){echo $res['nr_telefone'];} ?>" required>
            <br>
            <label for="email">E-mail: </label>
            <input type="text" id="email" name="email" value="<?php if(isset($res)){echo $res['ds_email'];} ?>" required>
            <br>
            <input type="submit" value="<?php if(isset($res)){echo "Atualizar";}else{echo "Cadastrar";} ?>">
        </form>
    </section>
    <br><br>


    <section id="tabela">

        <?php



            //Validar dados obtidos para o banco de dados
            if(isset($_POST['name'])){

                // Processo de alteracao de dados
                if(isset($_GET['id_up']) && !empty($_GET['id_up'])){

                    $id_upd = $_GET['id_up'];
                    $nome = $_POST["name"];
                    $sexo = $_POST['gender'];
                    $nascimento = $_POST['b_date'];
                    $telefone = $_POST['number'];
                    $email = $_POST['email'];

                    //Atualizar dados
                    $cmd = $pdo->prepare("UPDATE tb_pessoa SET ds_nome = :n, cd_sexo = :s, dt_nasc = :dn, nr_telefone = :t, ds_email = :e WHERE id_pessoa = :id");
                    $cmd->bindValue(":id",$id_upd);
                    $cmd->bindValue(":n",$nome);
                    $cmd->bindValue(":s",$sexo);
                    $cmd->bindValue(":dn",$nascimento);
                    $cmd->bindValue(":t",$telefone);
                    $cmd->bindValue(":e",$email);
                    $cmd->execute();
                    header("location: inicio.php");
                }else{

                    //Processo de cadastrar dados pela primeira vez
                    $nome = $_POST["name"];
                    $sexo = $_POST['gender'];
                    $nascimento = $_POST['b_date'];
                    $telefone = $_POST['number'];
                    $email = $_POST['email'];
                    // echo $nome."<br>";
                    // echo $sexo."<br>";
                    // echo $nascimento."<br>";
                    // echo $telefone."<br>";
                    // echo $email."<br>";

                    //inserir dados no banco de dados
                    $cmd = $pdo->prepare("INSERT INTO tb_pessoa(ds_nome,cd_sexo,dt_nasc,nr_telefone,ds_email) VALUES (:n,:s,:nas,:t,:e)");
                    $cmd->bindValue(":n",$nome);
                    $cmd->bindValue(":s",$sexo);
                    $cmd->bindValue(":nas",$nascimento);
                    $cmd->bindValue(":t",$telefone);
                    $cmd->bindValue(":e",$email);
                    $cmd->execute();
                }

            }






            //pegar todas as informacoes do banco de dados para fazer a tabela
            $cmd = $pdo->prepare("SELECT * FROM tb_pessoa");
            $cmd->execute();
            $resultado = $cmd->fetchAll(PDO::FETCH_ASSOC);

            // echo"<pre>";
            // print_r($resultado);
            // echo"</pre>";

            //-------------ATE AQUI TA BOM-------------
        ?>

        <!-- Fazer tabela -->
        <table border='1'>
            <tr>
                <!-- <th>ID</th> -->
                <th>NOME</th>
                <th>GENERO</th>
                <th>DATA NASCIMENTO</th>
                <th>TELEFONE</th>
                <th colspan='3'>EMAIL</th>
            </tr>
            <?php
                for($i = 0; $i < count($resultado); $i++){
                    ?>
                    <tr>
                    <?php
                    foreach($resultado[$i] as $arr => $item){
                        if($arr != "id_pessoa"){
                            ?>
                            <td><?php echo $item; ?></td>
                            <?php
                        }
                    }
                    ?>
                    
                    <td><a href="inicio.php?id_up=<?php echo $resultado[$i]['id_pessoa']; ?>"><button>Editar</button></a></td>
                    <td><a href="inicio.php?id=<?php echo $resultado[$i]['id_pessoa']; ?>"><button>Excluir</button></a></td>
                    </tr>
                    <?php
                }

            ?>


        </table>
    </section>
    
</body>
</html>

<?php
    //Para deletar informacoes de uma pessoa do banco de dados
    if(isset($_GET['id'])){
        $id_pessoa = $_GET['id'];
        //echo $id_pessoa;

        $cmd = $pdo->prepare('DELETE FROM tb_pessoa WHERE id_pessoa = :id');
        $cmd->bindValue(":id", $id_pessoa);
        $cmd->execute();
        header("location: inicio.php"); //Atualiza a pagina
    }

?>
