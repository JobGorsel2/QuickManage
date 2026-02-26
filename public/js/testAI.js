 let explainSource = null;
function streamExplanation(question, where) {
   const explainDiv = document.getElementById("aiAnswer");// or aiExplain, depending on your HTML
  explainDiv.textContent = "";

  if (explainSource) explainSource.close();

  let hiddenOnFirstToken = false;

  explainSource = new EventSource(
    "/ai/nl2where-stream?question=" + encodeURIComponent(question)+
    "&where=" + encodeURIComponent(where)
  );

  explainSource.onmessage = (e) => {
    // ✅ hide loader as soon as we receive the first token/letter
    if (!hiddenOnFirstToken && e.data && e.data.trim() !== "") {
      hideLoader();
      hiddenOnFirstToken = true;
    }

    explainDiv.textContent += e.data;
  };

  explainSource.addEventListener("done", () => {
    // stream finished
    explainSource.close();
    explainSource = null;
  });

  explainSource.onerror = () => {
    hideLoader();
    explainSource.close();
    explainSource = null;
  };
}

function showLoader() {
  const el = document.getElementById("aiLoader");
  
  if (el) el.style.display = "block";
}

function hideLoader() {
  const el = document.getElementById("aiLoader");
  if (el) el.style.display = "none";
}
 

async function askAI() {
 
  console.log('askAI werkt')
  const projectSelect = document.getElementById("projectSelect");
  const projectcode = projectSelect ? projectSelect.value : "";
  console.log(projectcode);
  const layer = window.layer;
  console.log(layer);
  //  const query = layer.createQuery();
  // query.groupByFieldsForStatistics = ["projectcode"];
  // console.log(query.groupByFieldsForStatistics)
  const table = window.table;
  if (!layer || !table) return;

  const question = document.getElementById("q").value.trim();
  if (!question) return;

  // 🔴 ALWAYS reset UI state first
  showLoader();
  document.getElementById("aiAnswer").textContent = "";

  // 🔴 HARD STOP previous stream
  if (explainSource) {
    explainSource.close();
    explainSource = null;
  }

  try {
    const res = await fetch("/ai/nl2where", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document
          .querySelector('meta[name="csrf-token"]')
          .getAttribute("content")
      },
      body: JSON.stringify({ question })
    });

    const text = await res.text();
    console.log(text)
    let data;
    try { data = JSON.parse(text); } catch { data = null; }

    if (!res.ok || !data) {
      hideLoader();
      alert("AI error"); 
      console.log(res)  
      console.log(data)  
      return; 
    } 
    // console.log(data)
    
    
    if (!projectcode) {
      hideLoader();
      alert("Selecteer eerst een projectcode.");
      return;
    }

    const safeProject = projectcode.replace(/'/g, "''");
    const projectWhere = `projectcode = '${safeProject}'`;

    const aiWhere = data.where;
    console.log('where clause'+aiWhere)
    // ✅ combine both
    const combinedWhere = `${projectWhere} AND ${aiWhere}`;
    console.log('projectcode + where clause'+combinedWhere)

    // Apply filter
    layer.definitionExpression = combinedWhere;

    // query with the combined where
    const objectIds = await layer.queryObjectIds({ where: combinedWhere });
    table.highlightIds = objectIds ?? [];

    // stream explanation should also use the combined where
    streamExplanation(question, combinedWhere);

  } catch (err) {
    console.error(err);
    hideLoader();
  }

}

// ask button
document.addEventListener("DOMContentLoaded", () => {
  const askBtn = document.getElementById("askBtn");
  const q = document.getElementById("q");

  if (!askBtn || !q) {
    console.error("askBtn or q not found in DOM");
    return;
  }

  askBtn.addEventListener("click", askAI);
  q.addEventListener("keydown", (e) => {
    if (e.key === "Enter") askAI();
  });
});

 