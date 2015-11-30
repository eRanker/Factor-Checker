</div> <!-- /#page-wrapper -->



<footer class="footer">
    <div class="container">
        <div class="footertop">
            <a href="https://www.eranker.com/" title="GeoRanker - Analyse Rankings by Countries &amp; Cities" target="_blank" class="georankerfooterlogo">
                <img src="img/logo-white.png" alt="eRanker">
            </a>
            <nav id="footermenu">
                <ul>
                    <li><a href="/" title="API Overview and Developer's Guide">Overview</a></li>
                    <li><a href="/functions" title="General information about using our API">Functions</a></li>
                    <li><a href="/support" title="API Support">Support</a></li>
                    <li class="lastitem"><a href="https://www.georanker.com/contactus" target="_blank" title="Contact GeoRanker Support">Contact</a></li>
                </ul>
            </nav>
        </div>
    </div>
</footer>



</div> <!-- /#wrapper -->


<?PHP
if ($seocheck_error) {
    ?>
    <div class="modal fade" id="errormodal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Error</h4>
                </div>
                <div class="modal-body">
                    <p><?PHP echo $seocheck_error_msg; ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <?PHP
}
?> 


<!-- Theme JavaScript -->
<script src="/bower_components/jquery/dist/jquery.min.js"></script>
<script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="/js/highstock/js/highstock.js"></script>
<script src="/js/highstock/js/modules/exporting.js"></script>
<script src="/js/vendor/d3.min.js"></script>
<script src="/js/vendor/circles.min.js"></script>
<script src="//maps.google.com/maps/api/js?sensor=true"></script>
<script src="/js/gmap/gmap.js"></script>
<script src="/js/jquery-print/jQuery.print.js"></script>

<!-- Project Base JS -->
<script src="/js/base.js"></script>

<!-- Report Page JS -->
<script src="/js/report.js"></script>

</body>
</html>