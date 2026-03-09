<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>QuickDataViewer</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
<script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.9.2/proj4.js"></script>
<script src="https://cdn.jsdelivr.net/npm/ol@9.2.4/dist/ol.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://unpkg.com/shpjs@latest/dist/shp.min.js"></script>
<script src="https://unpkg.com/xlsx@latest/dist/xlsx.full.min.js"></script>
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" /> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/solid.min.css" integrity="sha512-yDUXOUWwbHH4ggxueDnC5vJv4tmfySpVdIcN1LksGZi8W8EVZv4uKGrQc0pVf66zS7LDhFJM7Zdeow1sw1/8Jw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/fontawesome.min.css" integrity="sha512-SgaqKKxJDQ/tAUAAXzvxZz33rmn7leYDYfBP+YoMRSENhf3zJyx3SBASt/OfeQwBHA1nxMis7mM3EV/oYT6Fdw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/regular.min.css" integrity="sha512-WidMaWaNmZqjk3gDE6KBFCoDpBz9stTsTZZTeocfq/eDNkLfpakEd7qR0bPejvy/x0iT0dvzIq4IirnBtVer5A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/svg-with-js.min.css" integrity="sha512-FTnGkh+EGoZdexd/sIZYeqkXFlcV3VSscCTBwzwXv1IEN5W7/zRLf6aUBVf2Ahdgx3h/h22HNzaoeBnYT6vDlA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/brands.min.css" integrity="sha512-9YHSK59/rjvhtDcY/b+4rdnl0V4LPDWdkKceBl8ZLF5TB6745ml1AfluEU6dFWqwDw9lPvnauxFgpKvJqp7jiQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

 <script src="https://cdn.jsdelivr.net/npm/ol-mapbox-style@9.0.0/dist/olms.js"></script>
  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,600;1,9..144,300&display=swap" rel="stylesheet" />
  <link rel="icon" type="image/x-icon" href="{{ asset('storage/fav-cm.png') }}">

  <link rel="stylesheet" href="{{ asset('css/dataviewer.css') }}" >
</head>
<body>

{{-- Scherm 1 --}}
   <div class="container fileSelectionViewer"    >
      <div class="row">
        <div class="offset-lg-2 col-lg-8">
          <div class="content-wrapper"> 
            <!-- header -->
            <section class="header">
              <div class="container">
                <div class="row header-logo">
                  <div class="col-lg-12 text-center">
                    <img src="{{ asset('storage/gkb-groen.png') }}" class="logo" />
                  </div>
                </div>
                <div class="row">
                  <div class="offset-lg-1 col-lg-10 text-center">
                    <br/> 
                      <h3>QuickDataViewer</h3>
                    <br/>
                    
                  </div>
                </div>
              </div>
            </section>
            <!-- end header  -->

            <!-- body  -->
            <section class="body">
              <div class="container">
                <div class="row">
                  <div class="col-lg-12 text-center">
                    <div id="screen-upload" class="screen active">

                      <div class="card-body">

                          <div id="drop-zone" class="drop-zone" role="button" tabindex="0" aria-label="Bestand kiezen of slepen">

                            <input type="file" id="file-input" accept=".zip" hidden />

                            <div class="drop-icon">
                              <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="8" y="40" width="48" height="16" rx="6" fill="currentColor" opacity="0.25"/>
                                <rect x="24" y="44" width="6" height="6" rx="3" fill="currentColor"/>
                                <path d="M32 8 L32 36" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                                <path d="M20 26 L32 38 L44 26" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                              </svg>
                            </div>

                            <p class="drop-label">
                              <strong>Kies een bestand</strong> of sleep het hierin.
                            </p>
                            <p class="drop-hint">Ondersteund: Shapefile (.zip)</p>

                          </div>

                          <div id="error-box" class="error-box hidden" role="alert">
                            <span class="error-icon"></span>
                            <p id="error-msg"></p>                            
                          </div>

                          <div id="loading-overlay" class="loading-overlay hidden" aria-live="polite">
                            <div class="spinner"></div>
                            <p id="loading-msg"> </p>
                          </div>

                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section>  

            <!-- end body  -->

            <!-- footer  -->
            <section class="footer">
              <div class="container">
                <div class="row">
                  <div class="col-lg-12 text-center">
                    <div class="footer-img">
                      <img src="{{ asset('storage/footer.png') }}" class="logo" />
                    </div>
                  </div>
                </div>
              </div>
            </section>
            <!-- end footer  -->
          </div>
        </div>
      </div>
    </div>




    {{-- Scherm 2 --}}

  <div class="container-fluid quickDataViewer p-0" id="map"   style="display:none;">
    <div class="header_dataviewer">
      <div class="row">
        <div class="col-lg-12 text-center p-1">
          <h4>QuickDataViewer</h4>
        </div>
      </div>
    </div>
    <div id="controlpanel_dataviewer" class="controlpanel_dataviewer">
      <div class="row">
        <div class="col-lg-12 text-center">
          <div class="menu_dataviewer">
            <button id="back-button" class="btn upload" data-action="back">Terug naar upload</button>
            <button id="zoom-button" class="btn zoomTodata" data-action="zoom">Zoom naar data</button>
            <button id="export-button" class="btn export" data-action="export">Exporteer naar Excel</button>
            
            <div id="layerNames" style="margin-top: 20px; padding: 10px; background: #f5f5f5; border-radius: 5px; display: none;"></div>
           <button id="clear-button" class="btn removeData" data-action="clear"><i class="fa-solid fa-trash"></i></button>
          </div>
        </div>
      </div>
    </div>
  
    <div class="footer_dataviewer">
      <div class="row">
        <div class="col-lg-12 text-center">
          <div class="footer-img">
            <img src="{{ asset('storage/footer.png') }}" class="logo" />
              
          </div>
        </div>
      </div>
    </div>
  </div>
   
 
  <script src="/js/app_quickdataviewer.js"></script>


</body>
</html>
