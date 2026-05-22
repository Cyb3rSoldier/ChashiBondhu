
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learn Farming</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="white text-black">

    <!-- Page Title -->
    <section class="max-w-[1800px] mx-auto px-6 py-8">

        <div class="mb-10">
            <h1 class="text-4xl font-bold text-green-500 mb-3">
                Learn Farming
            </h1>

            <p class="text-gray-400 text-lg">
                Watch modern agricultural and smart farming videos.
            </p>
        </div>

        <?php

        $videos = [
            ["id"=>"mkEsLdNKlPM","title"=>"30 ESSENTIAL Organic Farming Techniques For Small Farms & Market Gardens"],
            ["id"=>"kz0FxoHji8k","title"=>"মানিকগঞ্জের তরুণ কৃষক শহীদুলের ১৫ শতকের কৃষি সাম্রাজ্য"],
            ["id"=>"samPcxjxq90","title"=>"হযরতের ১০০০ বিঘা আয়তনের ফল বাগান"],
            ["id"=>"ALC2Qv-4sGg","title"=>"Apricots, the unique sweet and sour taste of summer"],
            ["id"=>"Vv7nJ3oF6z8","title"=>"This summer can’t be without watermelon"],
            ["id"=>"wxEMzyX6l50","title"=>"লেয়ার মুরগি পালন কতটুকু লাভজনক? শুরু করার আগে জানুন সবকিছু"],
            ["id"=>"dda2OdmmDLE","title"=>"উদ্যোক্তা সজল আহমেদের ব্যতিক্রমী বিদেশি ফলের গবেষণা"],
            ["id"=>"fbKS_un2D0Y","title"=>"এবার দেখুন বাংলাদেশের সবচেয়ে বড় ফল বাগান- র.ই মানিক চিত্রপুরী"],
            ["id"=>"5sPwV0YEpo0","title"=>"জাপানের ফল পার্সিমন চাষ করে আলোড়ন সৃষ্টি করলেন যশোরের কৃষক"],
            ["id"=>"MSqGOalsgnE","title"=>"দোকানের এলাচ থেকে চারা হবেই! ১০০% সফল হওয়ার টেকনিক"],
            ["id"=>"52huDH6DyrM","title"=>"নরসিংদীতে স্বাধীন চৌধুরীর কোয়েল খামার"],
            ["id"=>"0rCZqu11yRA","title"=>"মুরগি vs হাঁস 🐔🦆 | কোনটা বেশি লাভ? কম খরচে বেশি আয়!"],
            ["id"=>"4IHgx9NVA8o","title"=>"মাত্র ১ বিঘা জমিতে ৬ রকম আয়! কম খরচ ও বেশি লাভের স্মার্ট কৃষি মডেল 💰"],
            ["id"=>"GPDstTazHuw","title"=>"৮ শতাংশ জমি দিয়ে লাখপতি | ২০ হাজার টাকায় ৮টি ইনকাম সোর্সের গোপন প্ল্যান"],
            ["id"=>"FtJttJeM28c","title"=>"ছাদে ৩ ইনকাম! মুরগি, আজোলা আর হাইড্রোপনিক সবজির স্মার্ট ফার্মিং প্ল্যান"],
            ["id"=>"UQW7Tgtv_g8","title"=>"কম খরচে SMART মুরগির খামার! মাত্র ৫০০০ টাকায় শুরু করুন"],
            ["id"=>"sCRb74lLbC0","title"=>"২০টা ছাগল দিয়ে শুরু → ১ বছরে কত আয়? 🐐 আসল হিসাব খুলে বললাম!"],
            ["id"=>"Knw33QiTjcg","title"=>"এক খরচে ৩ দিক থেকে আয়! 😳 Integrated Farming Model 💰"],
            ["id"=>"LGF33NN4B8U","title"=>"farming is science. process of growing fresh vegetables by Korean scientists. "],
            ["id"=>"oXJ26J-Fe1w","title"=>"How to Grow Super Fresh Celery Outdoors for Beginners"],
            ["id"=>"HxwaO8kOhOU","title"=>"This Is How I Grow Asparagus On The Terrace For More Sprouts, Fast Harvest"]
        ];

        ?>

        <!-- Video Grid -->
        <div id="videoContainer"
             class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">

            <?php foreach($videos as $index => $video):

                $thumbnail = "https://img.youtube.com/vi/".$video['id']."/hqdefault.jpg";
                $youtubeLink = "https://www.youtube.com/watch?v=".$video['id'];

                $hiddenClass = $index >= 15 ? 'hidden extra-video' : '';
            ?>

            <!-- Video Card -->
            <div class="video-card <?php echo $hiddenClass; ?>">

                <a href="<?php echo $youtubeLink; ?>" target="_blank">

                    <!-- Thumbnail -->
                    <div class="rounded-xl overflow-hidden bg-black">
                        <img src="<?php echo $thumbnail; ?>"
                             alt="<?php echo $video['title']; ?>"
                             class="w-full h-44 object-cover hover:scale-105 transition duration-300">
                    </div>

                    <!-- Video Info -->
                    <div class="flex gap-3 mt-3">

                        <!-- Channel Icon -->
                        <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-seedling text-white"></i>
                        </div>

                        <!-- Text -->
                        <div>
                            <h3 class="font-semibold text-sm leading-5 line-clamp-2 hover:text-green-400 transition duration-200">
                                <?php echo $video['title']; ?>
                            </h3>

                            <p class="text-gray-400 text-xs mt-1">
                                ChashiBondhu Academy
                            </p>

                            <p class="text-gray-500 text-xs">
                                Agricultural Education
                            </p>
                        </div>
                    </div>
                </a>
            </div>

            <?php endforeach; ?>

        </div>

        <!-- Load More Button -->
        <div class="text-center mt-12">
            <button id="loadMoreBtn"
                    class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-xl font-semibold transition duration-300 shadow-lg">
                Load More Videos
            </button>
        </div>

    </section>


    <script>

        const loadMoreBtn = document.getElementById('loadMoreBtn');
        const hiddenVideos = document.querySelectorAll('.extra-video');

        let currentItems = 15;

        loadMoreBtn.addEventListener('click', () => {

            let shown = 0;

            hiddenVideos.forEach(video => {

                if(video.classList.contains('hidden') && shown < 5){
                    video.classList.remove('hidden');
                    shown++;
                }
            });

            currentItems += shown;

            if(currentItems >= hiddenVideos.length + 15){
                loadMoreBtn.style.display = 'none';
            }
        });

    </script>

    <!-- Home Button -->
    <div class="text-center mt-12">
        <a href="index.php"
           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-xl">
            <i class="fa-solid fa-house"></i> Back to Home
        </a>
    </div>
</body>
</html>