<?php
	get_header();
	if ( have_posts() ) : while ( have_posts() ) : the_post();

	// Get Banner date
	$bannerData = array(
		'type' => get_field('banner_type'),
		'title' => get_field('banner_title')
	),

	echo"<pre>";
	print_r($bannerData);
	echo"</pre>";

?>

<?php // Banner ?>

<div id="video" class="video-bg iq-bg iq-bg-fixed iq-over-blue-80" data-vide-bg="video/01" data-vide-options="position: 0% 50%" style="width: 100%; height:100%;">
<section id="iq-home" class="iq-banner overview-block-pt">
    <div class="container-fluid">
        <div class="banner-text">
            <div class="row">
                <div class="col-md-6">
                    <h1 class="text-uppercase iq-font-white iq-tw-3">We are building <b class="iq-tw-7">software</b> to help</h1>
                    <p class="iq-font-white iq-pt-15 iq-mb-40">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley,</p>
                    <a href="video/01.mp4" class="iq-video popup-youtube"><i class="ion-ios-play-outline"></i></a>
                    <div class="iq-waves">
                        <div class="waves wave-1"></div>
                        <div class="waves wave-2"></div>
                        <div class="waves wave-3"></div>
                    </div>
                    <a href="#" class="button bt-black iq-mt-10 iq-ml-40">Download</a>
                </div>
                <div class="col-md-6">
                    <img class="banner-img" src="images/banner/01.png" alt="">
                </div>
            </div>
        </div>
    </div>
    <div class="banner-objects">
        <span class="banner-objects-01" data-bottom="transform:translatey(50px)" data-top="transform:translatey(-50px);">
                <img src="images/drive/03.png" alt="drive02">
            </span>
        <span class="banner-objects-02 iq-fadebounce">
                <span class="iq-round"></span>
            </span>
        <span class="banner-objects-03 iq-fadebounce">
                <span class="iq-round"></span>
        </span>
    </div>
</section>
</div>


<?php // End Banner ?>

<?php
	endwhile;
	endif;
	get_footer();
?>
