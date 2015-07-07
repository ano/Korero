<!DOCTYPE HTML>
<!--
	
	Author: Ano Tisam
	Email: an0tis@gmail.com
	Website: http://www.whupi.com
	Descrition: Search Front-End for Korero Open Dictionary
	
	Spectral by HTML5 UP
	html5up.net | @n33co
	License: Free for personal and commercial use licensed under the Creative Commons Attribution 3.0 License, which means you can:

    Use them for personal stuff
    Use them for commercial stuff
    Change them however you like

	... all for free, yo. In exchange, just give the AUthor credit for the program and tell your friends about it :)
-->
<html>

<head>
    <title>Simple Online Dictionary Database by the Cook Islands Maori Database</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <!--[if lte IE 8]><script src="assets/css/ie/html5shiv.js"></script><![endif]-->
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="assets/css/custom.css" />
    <!--[if lte IE 9]><link rel="stylesheet" href="assets/css/ie/v9.css" /><![endif]-->
    <!--[if lte IE 8]><link rel="stylesheet" href="assets/css/ie/v8.css" /><![endif]-->
</head>

<body class="landing">

    <!-- Page Wrapper -->
    <div id="page-wrapper">

        <!-- Header -->
        <header id="header" class="alt">
            <h1><a href="index.html">Cook Islands Maori Database</a></h1>
            <nav id="nav">
                <ul>
                    <li class="special">
                        <a href="#" class="menuToggle"><span>Menu</span></a>
                        <div id="menu">
                            <ul>
                                <li><a href="index.php">Home</a>
                                </li>
                                <li><a href="manage/wordslist.php">Words</a>
                                </li>
                                <li><a href="manage/dialectslist.php">Dialects</a>
                                </li>
                                <li><a href="manage/_languagelist.php">Language</a>
                                </li>
								<li><a href="manage/wordsadd.php">Add a New Word</a></li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </nav>
        </header>

        <!-- Banner -->
        <section id="banner">
            <div class="inner">
                <h2>Korero</h2>
                <form id="search" action="">
                    <input id="search_query" type="text">
                </form>
                <ul class="actions">
                    <li><a id="run_search" href="#banner" class="button special">Search Dictionary</a>
                    </li>
                </ul>
            </div>
            <a href="#one" class="more scrolly">Learn More</a>
        </section>
        <!-- Results -->
        <section id="two" class="wrapper style2 special hidden">
            <div id="searchresults" class="inner">
                <header class="major">
                    <p>Start Typing to search...</p>
                </header>
            </div>
        </section>
        <!-- One -->
        <section id="one" class="wrapper style1 special">
            <div class="inner">
                <header class="major">
                    <h2>About</h2>
                    <p>This is a free web application for other Pacific languages to build their own
                        <br /> language dictionary. This simple web app was designed and developed as part of the <a href="http://www.maori.org.ck/">Cook Islands Maori Database Project</a>. We're giving it away for free so you can start your own database project.</p>
                </header>
                <ul class="icons major">
                    <li><span class="icon fa-diamond major style1"><span class="label">PHP</span></span>
                    </li>
                    <li><span class="icon fa-heart-o major style2"><span class="label">MySQL</span></span>
                    </li>
                    <li><span class="icon fa-code major style3"><span class="label">HTML5Up Template</span></span>
                    </li>
                </ul>
            </div>
        </section>
        <!-- CTA -->
        <section id="cta" class="wrapper style4">
            <div class="inner">
                <header>
                    <h2>Overview</h2>
                    <p>The application is split into a backend and a front-end, you are currently on the Front-End of the system. Click Manage to go to the Back-End of the system and manage the vocabulary.</p>
                </header>
                <ul class="actions vertical">
                    <li><a href="#" class="button fit special">Manage</a>
                    </li>
                    <li><a href="#three" class="button fit">Learn More</a>
                    </li>
                </ul>
            </div>
        </section>
        <!-- Three -->
        <section id="three" class="wrapper style3 special">
            <div class="inner">
                <header class="major">
                    <h2>Technology Stack</h2>
                    <p>The Simple Online Dictionary Database web application that can run off either AMP stack (e.g. LAMP, Wamp or MAMP stack). To <a href="">download</a> it, unzip it and change the details in the configuration file database credentials (located in manage/ewcfg10.php on line 46) to match your database details and you should be good to go</p>
                </header>
                <ul class="features">
                    <li class="icon fa-paper-plane-o">
                        <h3>Linux</h3>
                        <p>We prefer to use a TurnkeyLinux LAMP, a free and open source LAMP Stack</p>
                    </li>
                    <li class="icon fa-laptop">
                        <h3>Apache</h3>
                        <p>We use the Apache webserver to server content, although any webservers can be used such as ngnix etc..</p>
                    </li>
                    <li class="icon fa-code">
                        <h3>MySQL</h3>
                        <p>We use a MySQL database to store dictionary data</p>
                    </li>
                    <li class="icon fa-headphones">
                        <h3>PHP</h3>
                        <p>The application backend is developed in PHP</p>
                    </li>
                    <li class="icon fa-heart-o">
                        <h3>HTML5</h3>
                        <p>The Front-End template is from HTML5 Up</p>
                    </li>
                    <li class="icon fa-flag-o">
                        <h3>Javascript</h3>
                        <p>We use a simple custom built javascript plugin to interface with our custom built SQL queries to return back relevant results</p>
                    </li>
                </ul>
            </div>
        </section>

        <!-- Footer -->
        <footer id="footer">
            <ul class="icons">
                <li><a href="#" class="icon fa-twitter"><span class="label">Twitter</span></a>
                </li>
                <li><a href="#" class="icon fa-facebook"><span class="label">Facebook</span></a>
                </li>
                <li><a href="#" class="icon fa-instagram"><span class="label">Instagram</span></a>
                </li>
                <li><a href="#" class="icon fa-dribbble"><span class="label">Dribbble</span></a>
                </li>
                <li><a href="#" class="icon fa-envelope-o"><span class="label">Email</span></a>
                </li>
            </ul>
            <ul class="copyright">
                <li>&copy; Simple Dictionary</li>
                <li>Design: <a href="http://html5up.net">HTML5 UP</a>
                </li>
            </ul>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery.scrollex.min.js"></script>
    <script src="assets/js/jquery.scrolly.min.js"></script>
    <script src="assets/js/skel.min.js"></script>
    <script src="assets/js/init.js"></script>
    <script src="assets/js/fuse.min.js"></script>
    <script src="assets/js/search.js"></script>

</body>
</html>