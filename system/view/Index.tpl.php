Dobrodošli u MVC aplikaciju<br><br>

<?php foreach($data['resources'] as $key => $data){?>
    <strong>
        <?=$key?>,
        http://localhost/projektDP/Project/?page=<?=$data['url']?>
    </strong>
    <br>
    <?=$data['description']?>
    <br>
    <br>
    <?=$data['method']?>
    <br><hr><br>
<?php }?>


<br>


*** search function vraca ili podatke za tocan dan/vikend/mjesec ili ako je taj dan vikend onda vraca priblizne podatek****