<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AIController extends Controller
{
    public function index() 
    {   
        $client_id = env('ARCGIS_CLIENT_ID');

        // $token = session('arcgis.access_token'); // if you have it
        // // dd($token);

        // $layerUrl = 'https://services9.arcgis.com/CjT8oELYhF7fnj6q/arcgis/rest/services/Testpunten/FeatureServer/0';
        // $allowedFields = $this->getLayerFields($layerUrl, $token);


        // ophalen van projectcodes
        $layerUrl = 'https://services9.arcgis.com/CjT8oELYhF7fnj6q/arcgis/rest/services/GKB_DL_Bomen/FeatureServer/0/query';
        $token = session('arcgis.access_token');
        $params = [
            'f' => 'json',
            'where' => '1=1',
            'returnGeometry' => 'false',
            // group + stats
            'groupByFieldsForStatistics' => 'projectcode',
            'outStatistics' => json_encode([
                [
                    'statisticType' => 'count',
                    'onStatisticField' => 'OBJECTID',
                    'outStatisticFieldName' => 'cnt',
                ]
            ]),
            // make sure group field is returned
            'outFields' => 'projectcode',
            // order by the output stat field name
            'orderByFields' => 'cnt DESC',
            // optional: include NULL group if the service allows it
            // 'where' => 'projectcode IS NOT NULL', // alternatively filter nulls out
        ];
        if (!empty($token)) {
            $params['token'] = $token;
        }

        $res = Http::withOptions(['verify' => false])
            ->get($layerUrl, $params)
            ->json();

        $features = $res['features'] ?? [];
        // dd($features);

        $data = collect($features)->map(function ($item) {
            return [
                'projectcode' => $item['attributes']['projectcode'] ?? null,
                'cnt' => $item['attributes']['cnt'] ?? 0,
            ];
        });
        // dd($data);
        //redirect
        return view('AI.index', [
            'client_id' => $client_id,
            'data' => $data
        ]);
    }

   public function callback(Request $request)
    {
        // dd($request);
        $code = $request->query('code');
        if (!$code) {
            abort(400, 'Missing authorization code (code).');
        }
         
        $portal = rtrim(config('services.arcgis.portal'), '/'); // https://www.arcgis.com

        //withOptions(['verify' => false]) alleen gebruiken in local developmode 

        $response = Http::withOptions(['verify' => false])->asForm()->post($portal . '/sharing/rest/oauth2/token', [
            'client_id'     => config('services.arcgis.client_id'),
            'client_secret' => config('services.arcgis.client_secret'),
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => 'http://localhost:3000/oauth-callback',
            'f'             => 'json',
        ]);

        if (!$response->ok()) {
            abort(500, 'Token endpoint error: ' . $response->body());
        }

        $data = $response->json();
        // dd($data);

        if (isset($data['error'])) {
            abort(500, 'ArcGIS token error: ' . json_encode($data['error']));
        }

        // ArcGIS typically returns: access_token, expires_in, username, ssl, refresh_token (depending on settings)
        $accessToken = $data['access_token'] ?? null;
        if (!$accessToken) {
            abort(500, 'No access_token returned: ' . json_encode($data));
        }

        // Store token in session (simple approach)
        session([
            'arcgis.access_token' => $accessToken,
            'arcgis.expires_in'   => $data['expires_in'] ?? null,
            'arcgis.username'     => $data['username'] ?? null,
        ]);

        return redirect()->route('testAI')->with('status', 'ArcGIS connected.');
    }

    /////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////
    // Functions Ollama call

    public function nl2where(Request $request)
    {
        $question = trim((string)$request->input('question', ''));
        abort_unless($question !== '', 422, 'Missing question');
        // dd($question);
        // Fields that the model is allowed to use (keep this tight!)
        $token = session('arcgis.access_token'); // if you have it
        // dd($token);
        $layerUrl = 'https://services9.arcgis.com/CjT8oELYhF7fnj6q/arcgis/rest/services/GKB_DL_Bomen/FeatureServer/0';
        $allowedFields = $this->getLayerFields($layerUrl, $token);

        // $layerUrl = 'https://services9.arcgis.com/CjT8oELYhF7fnj6q/arcgis/rest/services/GKB_DL_Bomen/FeatureServer/0';
        // $allowedFields = $this->getLayerFields($layerUrl, $token);
        // dd($allowedFields);
        // $allowedFields = ['BoomSoort', 'OBJECTID'];
        
        // dd($this->csv($allowedFields));
        // Give the model schema + strict output contract
        $system = <<<SYS
        You translate a user question into an ArcGIS Feature Layer SQL WHERE clause. The ArcGIS Feature Layer has features of trees.

        OUTPUT FORMAT (MANDATORY):
        - Output ONLY valid JSON (no markdown).
        - Keys: "where", "explanation".
        - NEVER translate any field name or field value.

        ALLOWED FIELDS:
        {$this->csv($allowedFields)}
        
        ALLOWED OPERATORS:
        *, =, <>, >, >=, <, <=, AND, OR, LIKE, IS NULL, IS NOT NULL
        
        IMPORTANT SQL RULES (STRICT):
        - Field names must NEVER be in quotes.
        - Only string VALUES are quoted, always with single quotes.
        - Do NOT quote numbers.
        - Do NOT invent field names.
        - Do NOT use SQL functions, subqueries, aliases, or semicolons. 
        - NEVER translate any field name or field value.
        - Return ONLY a valid JSON object.
        - Do NOT use markdown.
        - Do NOT wrap in ``` or ```json.
        - No extra text before or after the JSON.

        FINAL CHECK BEFORE RESPONDING:
        1) Field names are not quoted.
        2) Only allowed fields are used.
        3) Output is executable ArcGIS SQL.
        4) Return explanation in Dutch.
       

        If any rule cannot be satisfied, return:
        { "where": "1=0", "explanation": "Cannot build a valid filter." }
        SYS;

        $user = "Question: " . $question;

        // Ollama chat endpoint
        $ollama = Http::timeout(30)->post('http://127.0.0.1:11434/api/chat', [
            'model' => 'gemma3:12b',
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $user],
            ],
            'stream' => false,
            'options' => [
                'temperature' => 0.1,
            ],
        ]);

        if (!$ollama->ok()) {
            abort(500, 'Ollama error: ' . $ollama->body());
        }

        $content = data_get($ollama->json(), 'message.content');
//   dd($content);


        abort_unless(is_string($content), 500, 'Invalid Ollama response');

        $json = $this->extractJsonObject($content);
// dd($json);
        if (!$json || !isset($json['where'])) {
            \Log::error('AI returned invalid JSON', ['raw' => $content]);
            return response()->json(['error' => 'AI returned invalid JSON'], 502);
        }
        $where = $this->sanitizeWhere((string)$json['where'], $allowedFields);
        // dd($where);

        return response()->json([
            'where' => $where,
            'explanation' => (string)($json['explanation'] ?? ''),
        ]);
    }

        public function nl2whereStream(Request $request)
    {
        // dd($request);
        $question = (string) $request->query('question', '');
        $where    = (string) $request->query('where', '');

        if ($where === '') {
            abort(400, 'Missing where clause');
        }

      
        return new StreamedResponse(function () use ($question, $where) {
            // dd($where);
            $system = <<<SYS
            Je bent een GIS-assistent. Je krijgt een AL TOEGEPASTE selectie als ArcGIS SQL WHERE-clause.
            Deze WHERE is leidend en bestaat altijd.

            BELANGRIJK:
                - Begin je antwoord met het vertellen over wat er geselecteerd is.
                - De WHERE-clause hieronder is de waarheid. Gebruik exact dezelfde veldnamen en tekst.
                - Gebruik GEEN veldnamen uit de vraag als ze afwijken van de WHERE.
                - Je MOET de WHERE-clause exact teruggeven in je antwoord, tussen backticks.
                - Spreek de gebruiker altijd met u aan in je antwoord.
                - Maak opsommingen in je antwoord en geef het als een overzicht terug.
                                        
            WHERE (leidend):            
            {$where}                    
                                        
            Regels (STRICT):            
            - Maak GEEN aannames buiten deze WHERE.
            - Gebruik GEEN voorbeeld-taal zoals "als we een voorbeeld gebruiken".
            - Leg in het Nederlands in 5-7 zinnen uit wat de selectie betekent voor de gebruiker.
            - Gebruik SQL veldnamen uit de WHERE clause letterlijk in je antwoord terug.
            - Vertaal NOOIT veldnamen of waardes van deze WHERE.
            SYS;

            $ch = curl_init('http://127.0.0.1:11434/api/chat');

            $payload = json_encode([
                'model' => 'gemma3:12b',
                'stream' => true,
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => $question],
                ],
                'options' => ['temperature' => 0.1],
            ]);

            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_WRITEFUNCTION => function ($ch, $chunk) {
                    $lines = explode("\n", trim($chunk));

                    foreach ($lines as $line) {
                        $json = json_decode($line, true);
                        if (!isset($json['message']['content'])) continue;

                        echo "data: " . $json['message']['content'] . "\n\n";
                        ob_flush();
                        flush();
                    }

                    return strlen($chunk);
                }
            ]);

            curl_exec($ch);
            curl_close($ch);

        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no', // important for nginx
        ]);
    }
    // private function systemPrompt(): string
    // {
    //     return <<<SYS
    //     You are an assistant that explains GIS table selections in Dutch.
    //     Always explain what actual field names you used for the SQL where clause.
    //     SYS;
    // }
        

    // filters
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    private function csv(array $items): string
    {   
        
        return implode(', ', $items);
    }

    /**
     * Basic safety gate: block dangerous tokens + restrict field usage.
     * This is not a full SQL parser, but it prevents the worst problems.
     */
        private function sanitizeWhere(string $where, array $allowedFields): string
    {
        $w = trim($where);

        // block obvious injection / unsupported constructs
        $blocked = [';', '--', 'DROP', 'DELETE', 'INSERT', 'UPDATE', 'SELECT', 'EXEC', 'UNION'];    
        foreach ($blocked as $b) {  
            if (stripos($w, $b) !== false) {    
                \Log::warning('Rejected WHERE', ['where' => $w, 'reason' => 'blocked_token', 'token' => $b]);
                return "hier gaatie fout";   
            }
        }

        // Remove string literals before identifier parsing:
        // Replaces 'Wilg' with '' so Wilg won't be treated as an identifier.
        $wNoStrings = preg_replace("/'(?:''|[^'])*'/", "''", $w) ?? $w; 

        // // Build case-insensitive allowed field lookup
        $allowedMap = array_fill_keys(array_map('strtolower', $allowedFields), true);

        // // Extract identifiers from the string-stripped WHERE clause
        preg_match_all('/\b[A-Za-z_][A-Za-z0-9_]*\b/', $wNoStrings, $matches);
        $idents = array_unique($matches[0] ?? []);

        $keywords = ['AND','OR','LIKE','IS','NULL','NOT','IN','BETWEEN'];
        
        foreach ($idents as $id) {
            $upper = strtoupper($id);
            if (in_array($upper, $keywords, true)) continue;

            // If identifier isn't an allowed field, reject 
            if (!isset($allowedMap[strtolower($id)])) {
                \Log::warning('Rejected WHERE', ['where' => $w, 'reason' => 'field_not_allowed', 'identifier' => $id]);
                return "hier gaatie fout1";
            }
        }

        return $w === '' ? "hier gaatie fout2" : $w;
    }
 
    private function getLayerFields(string $layerUrl, string $token): array
    {
        $res = Http::withOptions(['verify' => false])->get($layerUrl, [
            'f' => 'json',
            'token' => $token,
        ])->json();

        $fields = $res['fields'] ?? [];
        // dd($fields);
        return array_values(array_map(fn($f) => $f['name'], $fields));
    }


        private function extractJsonObject(string $text): ?array
    {
        $t = trim($text);

        // 1) If it contains a fenced block ```json ... ```
        if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/i', $t, $m)) {
            $t = trim($m[1]);
        }

        // 2) Extract first {...} block as a fallback
        if (!str_starts_with($t, '{')) {
            if (preg_match('/\{[\s\S]*\}/', $t, $m2)) {
                $t = trim($m2[0]);
            }
        }

        $decoded = json_decode($t, true);
        return is_array($decoded) ? $decoded : null;
    }



    
}
