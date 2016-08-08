<?php
function get_staff_codes(&$codes){
    // This is for the staff tags
    $staff = array(
        "anna",
        "ali",
        "alura",
        "aly",
        "beth",
        "cari",
        "con",
        "cort",
        "eve",
        "ff",
        "jade",
        "jas",
        "julia",
        "lan",
        "mar",
        "nyu",
        "pyro",
        "rad",
        "reg",
        "sam",
        "san",
        "sar",
        "xx"
    );

    $hsArray = array("alura", 
                    "sar", 
                    "cort", 
                    "tina", 
                    "sam", 
                    "cari", 
                    "beth",
                    "caleb",
                    "jen");
    $nonStaff =  array_intersect($hsArray, $staff);
    foreach($staff as $s) {
        array_push($codes, array(
            'tag' => $s,
            'before' => '<span class="'.$s.'">',
            'after' => '</span>',
        ));
    }
    //return $codes;
}
?>