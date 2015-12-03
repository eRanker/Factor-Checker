<?php
$allfactors = (array) $erapi_allfactors;
$factor = '';
if (isset($_GET['factor']) && !empty($_GET['factor'])) {
    if (array_key_exists ($_GET['factor'], $allfactors)) {
        $factor = $allfactors[$_GET['factor']];
    }
}

?>
<main id="main" role="main">
    <section class="search-block">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <form method="POST" action="">
                        <div class="field-row">
                            <div class="field">
                                <input id="sc_url" type="url" placeholder="http://" name="sc_url" value="<?php echo (isset($_POST['sc_url']) ? htmlspecialchars($_POST['sc_url']) : ''); ?>">
                            </div>
                            <input type="hidden" name="factorsGroup[]" value="<?= (isset($_GET['factor']) && !empty($_GET['factor'])) ? $_GET['factor'] : '' ?>">
                            <input class="btn btn-default analyze-button" type="submit" value="Create the report">                            
                        </div>
                        <strong class="title">Enter your website URL and start the free test!</strong>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <section class="info-block">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <h2>Here’s what happens when the test starts</h2>
                    <div class="row holder">
                        <div class="col-sm-4 col-xs-12">
                            <span class="num">1</span>
                            <h3>Collect</h3>
                            <p>First we gather all resources from your URL (html, css, js, images, fonts, etc.)</p>
                        </div>
                        <div class="col-sm-4 col-xs-12">
                            <span class="num">2</span>
                            <h3>Emulate</h3>
                            <p>Then we fetch those resources from our server, on <?php echo $factor->text->friendly_name ?></p>
                        </div>
                        <div class="col-sm-4 col-xs-12">
                            <span class="num">3</span>
                            <h3>Analyze</h3>
                            <p>Finally, we review the data and give you some neat stats on how load times would differ between the two protocols.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php 
echo "<pre>";
var_dump($factor);
echo "</pre>"
?>
