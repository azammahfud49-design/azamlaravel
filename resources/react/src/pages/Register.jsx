import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useForm } from "react-hook-form";
import api from "../services/api";

export default function Register() {
  const { register, handleSubmit, watch } = useForm();
  const navigate = useNavigate();

  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  const registerUser = async (data) => {
    setLoading(true);
    setError("");

    try {
      await api.post("/auth/register", {
        name: data.name,
        email: data.email,
        password: data.password,
        password_confirmation: data.password_confirmation,
      });

      navigate("/login");
    } catch (err) {
      setError(err.response?.data?.message || "Registrasi gagal");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div
      className="relative min-h-screen bg-cover bg-center flex items-center justify-start px-20"
      style={{
        backgroundImage: "url('/images/auth-banner.jpg')",
      }}
    >
      <div className="absolute inset-0 bg-blue-950/35"></div>

      <div className="relative z-10 w-full max-w-sm">
        <div
          className="backdrop-blur-md border border-white/25 rounded-[30px] shadow-[0_8px_32px_rgba(255,255,255,0.08)] p-8"
          style={{
            background:
              "linear-gradient(135deg, rgba(255,255,255,0.08), rgba(255,255,255,0.02))",
          }}
        >
          <div className="mb-6">
            <h2 className="text-white text-lg">
              Buat Akun di <b>AKADEMIK.</b>
            </h2>
            <p className="text-white/70 text-sm">
              Portal Data Mahasiswa Anda.
            </p>
          </div>

          <h1 className="text-white text-4xl font-bold mb-6">
            REGISTER
          </h1>

          {error && (
            <div className="mb-4 bg-red-500/20 border border-red-400 text-white rounded-xl p-3 text-sm">
              {error}
            </div>
          )}

          <form onSubmit={handleSubmit(registerUser)}>
            <input
              {...register("name", { required: true })}
              type="text"
              placeholder="Nama Lengkap"
              className="w-full mb-3 px-5 py-3 bg-white/[0.04] border border-white/40 rounded-2xl text-white placeholder-white/60 outline-none focus:border-cyan-300 focus:bg-white/[0.06] transition"
            />

            <input
              {...register("email", { required: true })}
              type="email"
              placeholder="Email"
              className="w-full mb-3 px-5 py-3 bg-white/[0.04] border border-white/40 rounded-2xl text-white placeholder-white/60 outline-none focus:border-cyan-300 focus:bg-white/[0.06] transition"
            />

            <input
              {...register("password", { required: true, minLength: 6 })}
              type="password"
              placeholder="Kata Sandi"
              className="w-full mb-3 px-5 py-3 bg-white/[0.04] border border-white/40 rounded-2xl text-white placeholder-white/60 outline-none focus:border-cyan-300 focus:bg-white/[0.06] transition"
            />

            <input
              {...register("password_confirmation", {
                required: true,
                validate: (value) =>
                  value === watch("password") || "Password tidak sama",
              })}
              type="password"
              placeholder="Konfirmasi Kata Sandi"
              className="w-full mb-6 px-5 py-3 bg-white/[0.04] border border-white/40 rounded-2xl text-white placeholder-white/60 outline-none focus:border-cyan-300 focus:bg-white/[0.06] transition"
            />

            <button
              type="submit"
              disabled={loading}
              className="w-full py-3 rounded-full bg-gradient-to-r from-blue-500 to-cyan-400 hover:from-blue-600 hover:to-cyan-500 text-white font-bold shadow-lg transition disabled:opacity-60"
            >
              {loading ? "Memproses..." : "Daftar"}
            </button>
          </form>

          <div className="text-center mt-5">
            <Link
              to="/login"
              className="text-white/80 hover:text-white text-sm"
            >
              Sudah punya akun? Login
            </Link>
          </div>
        </div>
      </div>

      <p className="absolute bottom-5 left-0 right-0 text-center text-white/60 text-sm">
        © 2026 Universitas Indonesia Jaya. All rights reserved.
      </p>
    </div>
  );
}