<table style="width: 100%;">
    <tr>
        <td class="headerlogo z-50 w-40 md:w-56 lg:w-72" style="50%">
            <img class="-ml-2 lg:-ml-6" src="<?php echo PLUGIN_PATH; ?>/dist/pdf_logo.jpg" alt="" style="width: 200px;">
        </td>
        <td style="text-align:right;" style="50%; text-align:right;">
            <span style="font-size: 12px;"><?php echo date('j.n.Y'); ?></span><br>
            <a href="<?=get_site_url()?>"><?=get_site_url()?></a>
        </td>
    </tr>
</table>

<?=$report?>
<table style="width: 100%; margin-top: 40px;">
    <tr>
        <td width="33%">
            <img class="-ml-2 lg:-ml-6" src="<?php echo PLUGIN_PATH; ?>/dist/pdf_footer_logo1.jpg" alt="" style="width: 150px;">
        </td>
        <td width="33%" style="text-align:center;">
            <img class="-ml-2 lg:-ml-6" src="<?php echo PLUGIN_PATH; ?>/dist/pdf_footer_logo2.jpg" alt="" style="width: 180px;">
        </td>
        <td width="33%" style="text-align:right;">
            <img class="-ml-2 lg:-ml-6" src="<?php echo PLUGIN_PATH; ?>/dist/pdf_footer_logo3.jpg" alt="" style="width: 80px;">
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <p style="font-size: 14px;">
                Tämän julkaisun sisältö edustaa vain kirjoittajan näkemyksiä ja tekijä on niistä yksin vastuussa. Euroopan komissio ei ole vastuussa tämän julkaisun sisältämän aineiston käytöstä.
            </p>
        </td>
    </tr>
</table>