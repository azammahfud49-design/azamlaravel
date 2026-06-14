import { useEffect, useState } from "react";
import DashboardLayout from "../layouts/DashboardLayout";
import api from "../services/api";
import {
  FaUserGraduate,
  FaBook,
  FaChalkboardTeacher,
} from "react-icons/fa";

export default function Dashboard() {
  const [stats, setStats] = useState({
    total: 0,
    per_jurusan: [],
  });

  useEffect(() => {
    loadDashboard();
  }, []);

  const loadDashboard = async () => {
    try {
      const res = await api.get("/dashboard/stats");

      setStats(res.data.data);
    } catch (err) {
      console.error(err);
    }
  };

  return (
    <DashboardLayout>
      <div className="mb-8">
        <h1 className="text-4xl font-bold text-slate-800">
          Selamat Datang 👋
        </h1>

        <p className="text-slate-500 mt-2">
          Sistem Manajemen Data Mahasiswa
        </p>
      </div>

      <div className="grid md:grid-cols-3 gap-6 mb-8">

        <div className="bg-white rounded-2xl shadow-lg p-6">
          <FaUserGraduate size={40} className="text-blue-500" />
          <h2 className="mt-4 text-gray-500">Total Mahasiswa</h2>
          <p className="text-4xl font-bold">
            {stats.total}
          </p>
        </div>

        <div className="bg-white rounded-2xl shadow-lg p-6">
          <FaBook size={40} className="text-green-500" />
          <h2 className="mt-4 text-gray-500">Total Jurusan</h2>
          <p className="text-4xl font-bold">
            {stats.per_jurusan?.length || 0}
          </p>
        </div>

        <div className="bg-white rounded-2xl shadow-lg p-6">
          <FaChalkboardTeacher size={40} className="text-purple-500" />
          <h2 className="mt-4 text-gray-500">Mahasiswa Bulan Ini</h2>
          <p className="text-4xl font-bold">
            {stats.bulan_ini || 0}
          </p>
        </div>

      </div>
    </DashboardLayout>
  );
}