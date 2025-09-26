import axios from "axios";

const api = axios.create({
    baseURL: "http://127.0.0.1:8000/api",
});

api.interceptors.request.use(async (config) => {
    setInterval(async () => { 
        const token = localStorage.getItem("token");
        const expiry = localStorage.getItem("expires_in");
        
        if (expiry && Date.now() > expiry - 60000) {
        try {
            const refreshResponse = await axios.post("http://127.0.0.1:8000/api/auth/refresh", {}, {
                headers: { Authorization: `Bearer ${token}` }
            });
        
            const { access_token, expires_in } = refreshResponse.data;
            localStorage.setItem("token", access_token);
            localStorage.setItem("expires_in", Date.now() + expires_in * 1000);
            
            config.headers.Authorization = `Bearer ${access_token}`;
        } catch (err) {
            console.error("Refresh failed", err);
            localStorage.removeItem("token");
            localStorage.removeItem("expires_in");
            window.location.href = "/auth/login"; 
        }
        } else if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
    }, 60000);

    return config;
});

export default api;
