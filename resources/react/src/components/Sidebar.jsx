import { Link, useLocation } from "react-router-dom";
import {
  FaHome,
  FaUserGraduate,
  FaSignOutAlt,
  FaLeaf,
} from "react-icons/fa";
import api from "../services/api";

export default function Sidebar() {
  const location = useLocation();

  const logout = async () => {
    try {
      await api.post("/auth/logout");
    } catch {}
    localStorage.removeItem("token");
    window.location.href = "/login";
  };

  const menuClass = (path) =>
    `flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 mb-2 ${
      location.pathname === path
        ? "bg-green-500 text-white shadow-lg shadow-green-500/30"
        : "text-green-100 hover:bg-green-800/40 hover:translate-x-1"
    }`;

  return (
    <aside className="fixed left-0 top-0 w-72 h-screen bg-gradient-to-b from-green-950 via-green-900 to-green-800 text-white shadow-2xl">

      {/* Logo */}
      <div className="p-6 border-b border-green-700">
        <div className="flex items-center gap-3">
          <div className="bg-green-500 p-3 rounded-2xl shadow-lg">
            <FaLeaf size={24} />
          </div>

          <div>
            <h1 className="text-2xl font-bold">
              GreenCampus
            </h1>
            <p className="text-green-200 text-sm">
              Sistem Akademik
            </p>
          </div>
        </div>
      </div>

      {/* Menu */}
      <nav className="p-4">

        <Link to="/dashboard" className={menuClass("/dashboard")}>
          <FaHome size={18} />
          <span>Dashboard</span>
        </Link>

        <Link to="/mahasiswa" className={menuClass("/mahasiswa")}>
          <FaUserGraduate size={18} />
          <span>Mahasiswa</span>
        </Link>

      </nav>

      {/* Footer */}
      <div className="absolute bottom-0 left-0 w-full p-4 border-t border-green-700">

        <button
          onClick={logout}
          className="
            w-full
            flex items-center
            gap-3
            px-4
            py-3
            rounded-xl
            bg-red-500/20
            text-red-300
            hover:bg-red-500
            hover:text-white
            transition-all
            duration-300
          "
        >
          <FaSignOutAlt />
          Logout
        </button>

      </div>
    </aside>
  );
}