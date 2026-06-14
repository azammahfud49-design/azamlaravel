import { useForm } from "react-hook-form";
import { useState } from "react";
import { Link } from "react-router-dom";
import api from "../services/api";

export default function Register() {
  const { register, handleSubmit, watch } = useForm();

  const [error, setError] = useState(null);
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState(false);

  const registerUser = async (data) => {
    setLoading(true);
    setError(null);

    try {
      await api.post("/auth/register", data);
      setSuccess(true);
    } catch (err) {
      const messages = err.response?.data?.errors;
      if (messages) {
        const firstError = Object.values(messages)[0]?.[0];
        setError(firstError || "Registrasi gagal");
      } else {
        setError(err.response?.data?.message || "Registrasi gagal");
      }
      setLoading(false);
    }
  };

  if (success) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-slate-100">
        <div className="bg-white w-[480px] p-10 rounded-3xl shadow-2xl text-center">
          <div className="text-6xl mb-4">📧</div>
          <h2 className="text-3xl font-bold text-slate-800 mb-4">
            Cek Email Kamu!
          </h2>
          <p className="text-slate-500 mb-6">
            Kami sudah mengirim link verifikasi ke email{" "}
            <strong>{watch("email")}</strong>.
            <br />
            Silakan klik link tersebut untuk mengaktifkan akun.
          </p>
          <Link
            to="/login"
            className="inline-block py-3 px-8 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold hover:scale-105 transition"
          >
            Ke Halaman Login
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen flex">
      <div className="hidden lg:flex w-1/2 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 text-white items-center justify-center">
        <div className="max-w-md px-10">
          <h1 className="text-5xl font-bold mb-6">
            🎓 Sistem Akademik
          </h1>
          <p className="text-xl opacity-90">
            Daftar akun baru untuk mengelola data mahasiswa, dosen, jurusan, dan akademik by Azam Mahfud.
          </p>
        </div>
      </div>

      <div className="flex-1 flex items-center justify-center bg-slate-100">
        <form
          onSubmit={handleSubmit(registerUser)}
          className="bg-white w-[420px] p-10 rounded-3xl shadow-2xl"
        >
          <h2 className="text-4xl font-bold text-center text-slate-800 mb-2">
            Daftar
          </h2>
          <p className="text-center text-slate-500 mb-8">
            Buat akun baru
          </p>

          {error && (
            <div className="bg-red-100 text-red-600 p-3 rounded-xl mb-4">
              {error}
            </div>
          )}

          <div className="mb-4">
            <label className="block mb-2 text-slate-600">Nama</label>
            <input
              {...register("name")}
              placeholder="Nama Lengkap"
              className="w-full border border-slate-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div className="mb-4">
            <label className="block mb-2 text-slate-600">Email</label>
            <input
              {...register("email")}
              placeholder="admin@email.com"
              className="w-full border border-slate-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div className="mb-4">
            <label className="block mb-2 text-slate-600">Password</label>
            <input
              {...register("password")}
              type="password"
              placeholder="Minimal 6 karakter"
              className="w-full border border-slate-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div className="mb-6">
            <label className="block mb-2 text-slate-600">Konfirmasi Password</label>
            <input
              {...register("password_confirmation")}
              type="password"
              placeholder="Ulangi password"
              className="w-full border border-slate-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <button
            type="submit"
            disabled={loading}
            className="w-full py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold hover:scale-105 transition"
          >
            {loading ? "Memproses..." : "Daftar"}
          </button>

          <p className="text-center text-slate-500 mt-6">
            Sudah punya akun?{" "}
            <Link to="/login" className="text-blue-600 font-semibold hover:underline">
              Login
            </Link>
          </p>
        </form>
      </div>
    </div>
  );
}
