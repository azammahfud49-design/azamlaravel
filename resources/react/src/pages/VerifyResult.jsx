import { useSearchParams, Link } from "react-router-dom";

export default function VerifyResult() {
  const [searchParams] = useSearchParams();
  const status = searchParams.get("verification");
  const message = searchParams.get("message") || "";

  const config = {
    success: {
      icon: "✅",
      title: "Email Terverifikasi!",
      color: "text-green-600",
    },
    already: {
      icon: "ℹ️",
      title: "Sudah Diverifikasi",
      color: "text-blue-600",
    },
    error: {
      icon: "❌",
      title: "Verifikasi Gagal",
      color: "text-red-600",
    },
  };

  const current = config[status] || {
    icon: "❌",
    title: "Link Tidak Valid",
    color: "text-red-600",
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-slate-100">
      <div className="bg-white w-[480px] p-10 rounded-3xl shadow-2xl text-center">
        <div className="text-6xl mb-4">{current.icon}</div>
        <h2 className={`text-3xl font-bold mb-4 ${current.color}`}>
          {current.title}
        </h2>
        <p className="text-slate-500 mb-6">
          {message || "Terjadi kesalahan saat verifikasi"}
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
