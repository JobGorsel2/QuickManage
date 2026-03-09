 
const data = window.templateChoice;
    // const DBparameters = window.DBparameters;
     
    // console.log(DBparameters);
    
    const repo = data['repository'];
    const workspaceName = data['workspace'];
    
    if (data?.template?.name === "GKB Form template") {
        console.log('GKB Form template');
        (async () => {
                    
            // laden parameter html elementen naar html elementen voor form
            try {
                const repo = data.repository;
                const workspaceName = data.workspace;
                const response = await fetch(`https://fme-gkb.fmecloud.com/fmeapiv4/workspaces/${repo}/${workspaceName}`, {
                    method: "GET",
                    headers: {
                        "Authorization": "fmetoken token=653d48815e91626f06f6ed871b3810605193ac02",
                        "Accept": "application/json"
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                        
                const workspaceData = await response.json();
                console.log( 'Full response of the request: ');
                
                const parameters = workspaceData.parameters;
                const container = document.getElementById('GKB_Form_Template');
                // foreach alle parameters 

                console.log( parameters);

                parameters.forEach(param => {

                    const isNOTRequired =
                    param.required === false ||
                    param.required === 0 ||
                    param.required === "false" ||
                    param.required === "0";

                    // show * for required fields (your current logic is reversed)
                    const reqMark = isNOTRequired ? " " : "*";

                    const label = document.createElement('label');
                    label.textContent = (param.prompt || param.name) + reqMark;
                    label.setAttribute('for', param.name);

                    let input = null;
                    let selectFileText = null;
                    // console.log(param)
                    // check if parameter type is string
                    if (param.type === 'text') { 
                        // console.log(param.name)
                        // als parameter required is ( dus hij is niet optioneel )
                        if (param.type === 'text') {
                        input = document.createElement('input');
                        input.type = 'text';
                        input.name = param.name;
                        input.id = param.name;
                        input.value = param.defaultValue || '';
                        input.className = 'input-form';
                        if (!isNOTRequired) input.dataset.required = "true";
                        }

                        // check if parameter type is file
                    } else if (param.type === 'file' || param.type === 'list') {
                        selectFileText = document.createElement('label');
                        selectFileText.id = 'data_label';
                        selectFileText.textContent = 'Selecteer hier uw bestand';
                        selectFileText.className = 'btn btn-upload mb-3 file-btn text-center';

                        input = document.createElement('input');
                        input.type = 'file';
                        input.name = param.name;
                        input.id = `${param.name}_req`;           // keep one convention
                        input.className = 'position-absolute invisible';

                        selectFileText.setAttribute('for', input.id);
                        if (!isNOTRequired) input.dataset.required = "true";

                        input.addEventListener('change', (e) => {
                            const file = e.target.files[0];
                            selectFileText.textContent = file ? file.name : 'Selecteer hier uw bestand';
                        });
                        } 
                    else if (param.type === 'checkbox') {
                        input = document.createElement('input');
                        input.type = 'checkbox';
                        input.name = param.name;
                        input.id = param.name;
                        input.className = 'input-form-checkbox';
                        
                        if (!isNOTRequired) input.dataset.required = "true";
                    }

                    else if (param.type === 'datetime') {
                        input = document.createElement('input');
                        input.type = 'datetime-local';
                        input.name = param.name;
                        input.id = param.name;
                        input.className = 'input-form-datetime';
                        input.value = param.defaultValue;
                        if (!isNOTRequired) input.dataset.required = "true";
                    }

                    
                    else {
                        input = document.createElement('input');
                        input.type = 'text';
                        input.name = param.name;
                        input.id = param.name;
                        input.value = `Unsupported field type: ${param.type}`;
                    }

                    const inputWrap = document.createElement('div');
                    inputWrap.classList.add('input-wrap');

                    if (selectFileText) {
                        inputWrap.appendChild(label)
                        inputWrap.appendChild(selectFileText);
                    } else {
                        inputWrap.appendChild(label);
                    } 

                    if (!(input instanceof Node)) {
                    console.error("Skipping param because input is not a Node:", param, "input:", input);
                    return;
                    }

                    inputWrap.appendChild(input);
                    inputWrap.appendChild(document.createElement('br'));

                    container.appendChild(inputWrap);
                });

            } 
            
            catch (err) {
                console.error("Fetch failed:", err);
            }
             
    // next funtion
    
        })  ();

        function handleFormSubmit(event) {
            event.preventDefault(); // Stop default form submission

            const formContainer = document.getElementById('GKB_Form_Template');
            const inputs = formContainer.querySelectorAll('input');
            console.log(inputs)
            let inputFiles = false;
            let isValid = true;
        
            const formData = new FormData();
 
                inputs.forEach(input => {
                    const isNOTRequired = input.hasAttribute('data-required') && input.getAttribute('data-required') === 'true';
                    // console.log(input)
                if (isNOTRequired) {
                        if (input.type === 'file') {
                            const fileLabel = formContainer.querySelector(`label[for="${input.id}"]`);
                            // console.log(fileLabel)
                            if (input.files.length === 0) {
                                console.log('add class')
                                if (fileLabel) fileLabel.classList.add('outline_red2');
                                isValid = false;
                                return;
                            } else {
                                if (fileLabel) fileLabel.classList.remove('outline_red2');
                            }

                            formData.append("files", input.files[0], input.files[0].name);
                            inputFiles = true;
                            }

                        if (input.type === 'text') {
                            if (!input.value.trim()) {
                                document.getElementById(input.name).classList.add('outline_red2');
                                
                                isValid = false;
                                return;
                            }  else {
                                document.getElementById(input.name).classList.remove('outline_red2'); 
                            }
                        }
                        
                        if (input.type === 'checkbox') {
                            if (!input.value) {
                                document.getElementById(input.name).classList.add('outline_red2');
                                 
                                  
                                isValid = false;
                                return;
                            }  else {
                                document.getElementById(input.name).classList.remove('outline_red2'); 
                                
                            }
                            
                        }
                            console.log(input.value)

                        if (input.type === 'datetime') {
                            console.log(input.value)
                            if (!input.value) {
                                document.getElementById(input.name).classList.add('outline_red2');
                                 
                                  
                                isValid = false;
                                return;
                            }  else {
                                document.getElementById(input.name).classList.remove('outline_red2'); 
                                
                            }
                            
                        }
                        
                } else { }///

                            input.addEventListener('change', function () {
                                console.log(this.value);
                            });

                            /////
                });
                 
                if (!isValid) {
                        console.warn("Form not submitted due to validation errors.");
                        return;
                }
                if (isValid) {
                        console.log("Form correct!");
                         document.getElementById("mess1").style.display = 'block';
                        document.getElementById("loading").style.display = 'flex';
                      
                        if(inputFiles === true){
                            console.log(inputFiles)
                            uploadToServerAndStartWorkspace();
                        } else if ( inputFiles === false ) {

                            const workspaceParams = [];
                            inputs.forEach(input => {
                      
                                workspaceParams.push({
                                    name: input.name,
                                    value: input.value
                                });
 
                            });
                            
                            StartWorkspace(workspaceParams);
                            
                        }
                        // uploadToServerAndStartWorkspace();
                }
                // Optional: preview or debug
                for (let pair of formData.entries()) {
                console.log(`${pair[0]}:`, pair[1]);
                }

                // function uploadToServerAndStartWorkspace()
                function uploadToServerAndStartWorkspace() {
                    // here make post requests to fme server to upload the file, then after success response run function StartWorkspace that sends an post request to the api to run the workspace. POST with the input values and file names.
                        // upload files to /FME_SHAREDRESOURCE_TEMP/upload
                        // console.log(formData)


                         fetch('https://fme-gkb.fmecloud.com/fmeapiv4/resources/connections/FME_SHAREDRESOURCE_TEMP/upload?overwrite=true&path=%2FUploads_QuickManage', {
                                method: 'POST',
                                headers: {
                                    "Authorization": "fmetoken token=653d48815e91626f06f6ed871b3810605193ac02",
                                    "Accept":"*/*",
                                },
                                processData: false,
                                contentType: false,
                                body: formData
                            })
                            .then(response => {
                                if (!response.ok) throw new Error(`Upload failed. Status: ${response.status}`);
                                return response.json();
                            })
                            .then(uploadResult => {
                                console.log('File upload success:', uploadResult);

                                // STEP 2: Extract values from inputs again for workspace parameters
                                const workspaceParams = [];

                                inputs.forEach(input => {
                                    if (input.type === 'file' && input.files.length > 0) {
                                        workspaceParams.push({
                                            name: input.name,
                                            value: "$(FME_SHAREDRESOURCE_TEMP)/Uploads_QuickManage/" + input.files[0].name
                                        });
                                    } else {
                                        workspaceParams.push({
                                            name: input.name,
                                            value: input.value
                                        });
                                    }
                                });

                                // Call your workspace start function
                                StartWorkspace(workspaceParams);
                            })
                            .catch(error => {
                                console.error("Upload or start failed:", error);
                            });
                }

                function buildPublishedParametersObject(paramsArray) {
                const obj = {};

                paramsArray.forEach(p => {
                    obj[p.name] = p.value;
                });

                return obj;
                }

                function StartWorkspace(paramsArray) {
                    // run workspace with the publishedParameters
                    const repo = window.templateChoice.repository;
                    const workspaceName = window.templateChoice.workspace;

                    
                    const publishedParameters = buildPublishedParametersObject(paramsArray);
                    const body = {
                        repository: repo,
                        workspace: workspaceName,
                        publishedParameters: publishedParameters
                    };
                    // console.log(jsonArray)

                      fetch(`https://fme-gkb.fmecloud.com/fmeapiv4/jobs`, {
                        method: "POST",
                        headers: {
                            Authorization: "fmetoken token=653d48815e91626f06f6ed871b3810605193ac02",
                            "Content-Type": "application/json",
                            Accept: "application/json"
                        },
                        body: JSON.stringify(body)
                    
                    })
                    .then(res => {
                        if (!res.ok) throw new Error(`Workspace start failed: ${res.status}`);
                        return res.json();
                    })
                    .then(data => {
                        console.log(data)
 
                        document.getElementById("mess1").style.display = 'none';
                        document.getElementById("loading").style.display = 'none';
                        document.getElementById("mess2").style.display = 'block';
                        document.getElementById("mess2").innerHTML = 'De conversie is klaar!';
                    })
                    .catch(err => {
                        console.error("Request failed with status: " + err);
                        document.getElementById("loading").style.display = 'none';
                        document.getElementById("mess1").style.display = 'none';
                        document.getElementById("errorMessage").style.display = "block";
                        document.getElementById('errorMessage').innerHTML = 'Error... conversie kan niet gestart worden.<br/><div class="bold"> Neem contact op met Dirk-Jan of Job</div>';
                    });
                }

        }

        
    } 
 
    else {
        console.log("Template is not 'GKB Form template'; skipping fetch.");
    } 


    if (data?.template?.name === "GKB Rapport template") {
        console.log('GKB Rapport template is running')
    } 
    
    else {
        console.log("Template is not 'GKB Rapport template'; skipping fetch.");
    }

    