import { Routes, Route, Navigate } from "react-router-dom";
import Login from "./pages/Login";
import Register from "./pages/Register";
import VerifyEmail from "./pages/VerifyEmail";
import VerifyResult from "./pages/VerifyResult";
import Dashboard from "./pages/Dashboard";
import Mahasiswa from "./pages/Mahasiswa";

function App() {
  const token = localStorage.getItem("token");

  return (
    <Routes>
      <Route path="/login" element={token ? <Navigate to="/dashboard" /> : <Login />} />
      <Route path="/register" element={token ? <Navigate to="/dashboard" /> : <Register />} />
      <Route path="/verify-email/:id/:hash" element={<VerifyEmail />} />
      <Route path="/verify-result" element={<VerifyResult />} />
      <Route path="/dashboard" element={token ? <Dashboard /> : <Navigate to="/login" />} />
      <Route path="/mahasiswa" element={token ? <Mahasiswa /> : <Navigate to="/login" />} />
      <Route path="*" element={<Navigate to={token ? "/dashboard" : "/login"} />} />
    </Routes>
  );
}

export default App;
