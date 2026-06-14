import { useEffect, useState } from "react";
import { useParams, Link } from "react-router-dom";
import api from "../services/api";

export default function VerifyEmail() {
  const { id, hash } = useParams();
  const [status, setStatus] = useState("loading");
  const [message, setMessage] = useState("");

  useEffect(() => {
    const verify = async () => {
      try {
        const response = await api.get(`/auth/verify-email/${id}/${hash}`);
        setStatus("success");
        setMessage(response.data.message);
      } catch (err) {
        setStatus("error");
        setMessage(err.response?.data?.message || "Verifikasi gagal");
      }
    };

    verify();
  }, [id, hash]);

  return (
    <div className="min-h-screen flex items-center justify-center bg-slate-100">
      <div className="bg-white w-[480px] p-10 rounded-3xl shadow-2xl text-center">
        {status === "loading" && (
          <>
            <div className="text-6xl mb-4 animate-pulse">⏳</div>
            <h2 className="text-3xl font-bold text-slate-800 mb-4">
              Memverifikasi...
            </h2>
            <p className="text-slate-500">Mohon tunggu sebentar</p>
          </>
        )}

        {status === "success" && (
          <>
            <div className="text-6xl mb-4">✅</div>
            <h2 className="text-3xl font-bold text-green-600 mb-4">
              Email Terverifikasi!
            </h2>
            <p className="text-slate-500 mb-6">{message}</p>
            <Link
              to="/login"
              className="inline-block py-3 px-8 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold hover:scale-105 transition"
            >
              Ke Halaman Login
            </Link>
          </>
        )}

        {status === "error" && (
          <>
            <div className="text-6xl mb-4">❌</div>
            <h2 className="text-3xl font-bold text-red-600 mb-4">
              Verifikasi Gagal
            </h2>
            <p className="text-slate-500 mb-6">{message}</p>
            <Link
              to="/login"
              className="inline-block py-3 px-8 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold hover:scale-105 transition"
            >
              Ke Halaman Login
            </Link>
          </>
        )}
      </div>
    </div>
  );
}
