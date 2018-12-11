<?php
require '../functions.php';
    /*
    Where:
    modifydate = Math.round((new Date()).getTime() / 1000); 
    RadiomanID = Unique integer - try use modifydate above
    siteId = 1322 (test changes)
    slug / title / fieldTitle all the same - (mi-/nw1-/tw-)ClipNameWithNoSpaces

    Responce
    "Successful 10969970_21471137_10969971_JD-Test2"
    Save file with this name, using modified date above and the 3 ids from the response:

    inews_1543481759_10969970_21471137_10969971_1_twjdtest2_a_1543481759_rte54fminews.mp2
    */
    $siteId = 1322;
    $radiomanid = time(); //date();
    $modifydate = time();
    $slug = "TW-DB-Test";
    $title = "TW-DB-Test2";
    $fieldTitle = "TW-DB-Test";

echo getClipperFileName($siteId, $radiomanid, $modifydate, $slug, $title, $fieldTitle);
?>