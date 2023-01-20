<?php
header("Content-type: text/javascript");
$q = $_GET["q"];
$url = "https://myanimelist.net/search/all?q=$q&cat=all";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$headers = [
    "authority: myanimelist.net",
    "accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
    "accept-language: vi,en;q=0.9,en-US;q=0.8",
    "sec-fetch-dest: document",
    "sec-fetch-mode: navigate",
    "sec-fetch-site: same-origin",
    "sec-fetch-user: ?1",
    "upgrade-insecure-requests: 1",
    "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36 Edg/108.0.1462.76",
];
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
$resp = curl_exec($curl);
curl_close($curl);
$listOut = [];
$listAnime = explode('<div class="list di-t w100">', $resp);
for ($i = 1; $i < count($listAnime); $i++) {
    $aniobj = [];
    $content = explode(
        "<br>",
        strip_tags(htmlspecialchars_decode($listAnime[$i]), "<br>")
    );
    if (
        count($content) == 4 &&
        (strpos($content[0], "eps") == true ||
            strpos($content[0], "vols") == true)
    ) {
      	$rate = explode('<', explode('Scored ', $listAnime[$i])[1])[0];
      	$mens = trim(explode('<br>', explode('Scored', trim(explode(' members<br>', $listAnime[$i])[0]))[1])[1]);

        $slice = explode("add", $content[0]);
      	$aniobj["rate"] = $rate;
      	
      	
      	
      	if ($aniobj["rate"] < 3) $aniobj["Recommend_to_see"] = "Bad";
      	if ($aniobj["rate"] >= 3) $aniobj["Recommend_to_see"] = "Not good";
      	if ($aniobj["rate"] > 5) $aniobj["Recommend_to_see"] = "okay";
      	if ($aniobj["rate"] > 7) $aniobj["Recommend_to_see"] = "nice";
      	if ($aniobj["rate"] > 9) $aniobj["Recommend_to_see"] = "wonderful";
      
      
      	$aniobj["members"] = $mens;
        $aniobj["name"] = trim($slice[0]);
        $aniobj["type"] = trim($slice[1]);
        if (strpos($aniobj["type"], "eps")) {
            $aniobj["count_eps_or_vols"] = explode(
                "(",
                explode(" eps", $aniobj["type"])[0]
            )[1];
        } elseif (strpos($aniobj["type"], "vols")) {
            $aniobj["count_eps_or_vols"] = explode(
                "(",
                explode(" vols", $aniobj["type"])[0]
            )[1];
        }
        $img = explode(
            '"',
            explode('<img class="lazyload" data-src="', $listAnime[$i])[1]
        )[0];
        $aniobj["img"] = $img;
        $link = explode('"', explode('<a href="', $listAnime[$i])[1])[0];
        $aniobj["link"] = $link;
        $listOut[] = $aniobj;
    }
}
die(json_encode($listOut, JSON_PRETTY_PRINT));
?>
