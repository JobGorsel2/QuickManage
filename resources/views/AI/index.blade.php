@if(session()->has('arcgis.access_token'))

@extends('layouts.aiTest')

@section('content')
    <div class="container-fluid title_container">
        <div class="row">
                <div class="col-lg-12 text-center" >
                    <h2 class="c-white">Test Ollama AI met ArcGIS Online data
                        <a href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="btn btn-danger logoutBTN">
                            <i class="fa-solid fa-right-from-bracket c-white"></i>
                        </a>
                        <form method="POST" id="logout-form" action="{{ route('logoutAGOL') }}" style="display:none"> @csrf </form>
                    </h2>
                </div>
        </div>
    </div>
    
    <div class="container-fluid tableContainer">
        <div class="row">
            <div class="col-lg-8">
                <div id="tableDiv"> </div>
            </div>
            <div class="col-lg-4 AIChatbox">
                    <div id="aiLoader" style="display:none;" class="pt-1" >
                        <span class="aiLoadingText pt-2">AI is heel hard aan het nadenken…</span>
                    </div>  
                <div id="aiExplain" class="explenationAI pt-3">
                    <p>
                        Met welk project kan ik u helpen?
                        <select id="projectSelect" name="projectcodes">
                        <option value="">- Selecteer Projectcode -</option>
                        @foreach($data as $row)
                            <option value="{{ $row['projectcode'] ?? '' }}">
                            {{ $row['projectcode'] ?? 'leeg' }}
                            </option>
                        @endforeach
                        </select>
                    </p>

                    <!-- ✅ this is where streaming text goes -->
                    <div id="aiAnswer"></div>

                </div>
                <div id="chatBox" class="input_chatbox">
                
                <input id="q" placeholder="Stel een vraag over deze data..." />
                    <button id="askBtn" class="askBtn"><i class="fa-solid fa-arrow-up"></i></button>
                </div>
                
            </div>
        </div>
        <div class="row"> 
            <div class="col-lg-12 m-3"></div>
        </div>
    </div>


<script>
    require([
    "esri/identity/OAuthInfo",
    "esri/identity/IdentityManager",
    "esri/layers/FeatureLayer",
    "esri/widgets/FeatureTable"
    ], function (OAuthInfo, esriId, FeatureLayer, FeatureTable) {

    const info = new OAuthInfo({
        appId: "{{ config('services.arcgis.client_id') }}",
        popup: false
    });

    esriId.registerOAuthInfos([info]);

    console.log(esriId)

    const layer = new FeatureLayer({
        url: "https://services9.arcgis.com/CjT8oELYhF7fnj6q/arcgis/rest/services/GKB_DL_Bomen/FeatureServer/0"
    });

    layer.load().then(() => {
    const table = new FeatureTable({
      layer,
      container: "tableDiv"
    });
    window.layer = layer;
    window.table = table;
    

    layer.definitionExpression = "1=1";

    const sel = document.getElementById("projectSelect");
    const chatBox = document.getElementById("chatBox");
    if (!sel) {
      console.error("projectSelect not found");
      return;
    }

    sel.addEventListener("change", async () => {
      const projectcode = sel.value;

      // clear previous highlights
      table.highlightIds = [];

      if (!projectcode) {
        console.log('geen projectcode')
        layer.definitionExpression = "1=0";
        if (chatBox) chatBox.style.display = "none";
        document.getElementById("aiExplain").innerHTML =
          `<p>Met welk project kan ik u helpen?</p>`;
        return;
      }

      // escape single quotes
      const safe = projectcode.replace(/'/g, "''");
      const where = `projectcode = '${safe}'`;

      layer.definitionExpression = where;

      if (chatBox) chatBox.style.display = "flex";
     const aiAnswer = document.getElementById("aiAnswer");
        if (!aiAnswer) return;

        aiAnswer.textContent = `Project ${projectcode} geselecteerd. Stel je vraag hieronder.`;
    });
    }).catch(console.error);

    });
</script>
 


@endsection

@else

    <a href="https://gkb.maps.arcgis.com/sharing/oauth2/authorize?client_id={{ $client_id }}&response_type=code&redirect_uri=http://localhost:3000/oauth-callback" id="sign-in" class="btn btn-primary">Inloggen AGOL</a>

@endif