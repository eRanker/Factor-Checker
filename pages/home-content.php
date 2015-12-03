<main id="main" role="main">

<?php foreach ($allfactors as $value) { ?>
        
    <div class="container">
        <div class="row">
            <strong class="intro"><a href="index.php?p=createreport&factor=<?php echo $value->id ?>"><?php echo $value->text->friendly_name ?></a></strong>
        </div>
    </div>
<?php } ?>

</main>