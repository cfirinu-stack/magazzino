const API_BASE = "../backend/api/";

async function apiPost(endpoint, data){
    const res = await fetch(API_BASE + endpoint, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
    });
    return res.json();
}

async function apiGet(endpoint){
    const res = await fetch(API_BASE + endpoint);
    return res.json();
}
