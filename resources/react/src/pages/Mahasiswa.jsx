import { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import DashboardLayout from "../layouts/DashboardLayout";
import api from "../services/api";
import { FaPlus, FaEdit, FaTrash, FaSearch, FaDownload, FaUpload, FaTimes } from "react-icons/fa";

export default function Mahasiswa() {
  const [data, setData] = useState([]);
  const [meta, setMeta] = useState(null);
  const [search, setSearch] = useState("");
  const [loading, setLoading] = useState(false);
  const [modal, setModal] = useState(false);
  const [editId, setEditId] = useState(null);
  const [deleting, setDeleting] = useState(null);
  const [error, setError] = useState("");
  const [searchAlgorithm, setSearchAlgorithm] = useState("linear");
  const [sortType, setSortType] = useState("bubble");
  const [jsonFile, setJsonFile] = useState(null);

  const { register, handleSubmit, reset, setValue } = useForm();

  const fetchData = async (page = 1) => {
    setLoading(true);
    try {
      const params = {
  page,
  per_page: 10,
  algorithm: searchAlgorithm,
  sort: sortType,
    };
      if (search) params.search = search;
      const res = await api.get("/mahasiswa", { params });
      setData(res.data.data);
      setMeta(res.data.meta);
    } catch {
      setError("Gagal memuat data");
    }
    setLoading(false);
  };

  useEffect(() => {
    fetchData();
  }, []);

  const onSubmit = async (formData) => {
    try {
      if (editId) {
        await api.put(`/mahasiswa/${editId}`, formData);
      } else {
        await api.post("/mahasiswa", formData);
      }
      setModal(false);
      reset();
      setEditId(null);
      await fetchData();
    } catch (err) {
      const messages = err.response?.data?.errors;
      if (messages) {
        setError(Object.values(messages)[0]?.[0] || "Gagal menyimpan");
      } else {
        setError(err.response?.data?.message || "Gagal menyimpan");
      }
    }
  };

  const openEdit = async (id) => {
    try {
      const res = await api.get(`/mahasiswa/${id}`);
      const m = res.data.data;
      setEditId(id);
      setValue("nama", m.nama);
      setValue("nim", m.nim);
      setValue("jurusan", m.jurusan);
      setValue("fakultas", m.fakultas);
      setValue("email", m.email);
      setValue("nomor_hp", m.nomor_hp);
      setValue("alamat", m.alamat);
      setValue("tanggal_lahir", m.tanggal_lahir?.split("T")[0]);
      setValue("jenis_kelamin", m.jenis_kelamin);
      setModal(true);
    } catch {
      setError("Gagal memuat data");
    }
  };

  const confirmDelete = async () => {
    if (!deleting) return;
    try {
      await api.delete(`/mahasiswa/${deleting}`);
      setDeleting(null);
      await fetchData();
    } catch {
      setError("Gagal menghapus");
    }
  };

  const importJson = async () => {
  if (!jsonFile) {
    alert("Pilih file JSON terlebih dahulu");
    return;
  }

  try {
    const formData = new FormData();
    formData.append("file", jsonFile);

    await api.post("/mahasiswa/import-json", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });

    alert("Import berhasil");
    fetchData();
      } catch {
    alert("Import gagal");
      }
    };
  const openAdd = () => {
    setEditId(null);
    reset({ jenis_kelamin: "L" });
    setModal(true);
  };

  return (
    <DashboardLayout>
      <div className="mb-6 flex items-center justify-between">
        <h1 className="text-3xl font-bold text-slate-800">Data Mahasiswa</h1>
        <div className="flex gap-2 items-center">

          <a
            href="/api/mahasiswa/export/csv"
            className="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 flex items-center gap-2"
          >
            <FaDownload /> CSV
          </a>

          <a
            href="/api/mahasiswa/export/txt"
            className="px-4 py-2 bg-cyan-600 text-white rounded-xl hover:bg-cyan-700 flex items-center gap-2"
          >
            <FaDownload /> TXT
          </a>

          <a
            href="/api/mahasiswa/export/json"
            className="px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 flex items-center gap-2"
          >
            <FaDownload /> JSON
          </a>

          {/* IMPORT JSON */}
          <input
            type="file"
            accept=".json"
            onChange={(e) => setJsonFile(e.target.files[0])}
            className="border rounded-xl px-2 py-2"
          />

          <button
            onClick={importJson}
            className="px-4 py-2 bg-orange-600 text-white rounded-xl hover:bg-orange-700 flex items-center gap-2"
          >
            <FaUpload /> Import
          </button>

          <button
            onClick={openAdd}
            className="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 flex items-center gap-2"
          >
            <FaPlus /> Tambah
          </button>
        </div>
      </div>

      <div className="mb-6 flex items-center gap-2">
        {/* Search Algorithm */}
        <select
          value={searchAlgorithm}
          onChange={(e) => setSearchAlgorithm(e.target.value)}
          className="border border-slate-300 rounded-xl px-3 py-2"
        >
          <option value="linear">Linear Search</option>
          <option value="binary">Binary Search</option>
          <option value="sequential">Sequential Search</option>
        </select>

        {/* Sort Algorithm */}
        <select
          value={sortType}
          onChange={(e) => setSortType(e.target.value)}
          className="border border-slate-300 rounded-xl px-3 py-2"
        >
          <option value="bubble">Bubble Sort</option>
          <option value="merge">Merge Sort</option>
        </select>

        <button
          onClick={() => fetchData()}
          className="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700"
        >
          Cari
        </button>
      </div>

      {error && (
        <div className="bg-red-100 text-red-600 p-3 rounded-xl mb-4">{error}</div>
      )}

      <div className="overflow-x-auto">
        <table className="w-full">
          <thead>
            <tr className="bg-slate-100 text-left">
              <th className="p-3">NIM</th>
              <th className="p-3">Nama</th>
              <th className="p-3">Jurusan</th>
              <th className="p-3">Fakultas</th>
              <th className="p-3">Email</th>
              <th className="p-3">HP</th>
              <th className="p-3">JK</th>
              <th className="p-3">Aksi</th>
            </tr>
          </thead>
          <tbody>
            {loading ? (
              <tr><td colSpan={8} className="p-6 text-center text-slate-500">Memuat...</td></tr>
            ) : data.length === 0 ? (
              <tr><td colSpan={8} className="p-6 text-center text-slate-500">Tidak ada data</td></tr>
            ) : data.map((m) => (
              <tr key={m.id} className="border-b hover:bg-slate-50">
                <td className="p-3 font-mono">{m.nim}</td>
                <td className="p-3 font-medium">{m.nama}</td>
                <td className="p-3">{m.jurusan}</td>
                <td className="p-3">{m.fakultas}</td>
                <td className="p-3">{m.email}</td>
                <td className="p-3">{m.nomor_hp}</td>
                <td className="p-3">{m.jenis_kelamin}</td>
                <td className="p-3">
                  <div className="flex gap-2">
                    <button
                      onClick={() => openEdit(m.id)}
                      className="p-2 text-blue-600 hover:bg-blue-100 rounded-lg"
                    >
                      <FaEdit />
                    </button>
                    <button
                      onClick={() => setDeleting(m.id)}
                      className="p-2 text-red-600 hover:bg-red-100 rounded-lg"
                    >
                      <FaTrash />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {meta && (
        <div className="mt-4 flex items-center justify-between">
          <span className="text-sm text-slate-500">
            Hal {meta.current_page} dari {meta.last_page} (total {meta.total})
          </span>
          <div className="flex gap-2">
            <button
              disabled={meta.current_page <= 1}
              onClick={() => fetchData(meta.current_page - 1)}
              className="px-3 py-1 rounded-lg border disabled:opacity-50"
            >
              Prev
            </button>
            {Array.from({ length: meta.last_page }, (_, i) => i + 1)
              .filter((p) => Math.abs(p - meta.current_page) <= 2 || p === 1 || p === meta.last_page)
              .map((p, idx, arr) => (
                <span key={p}>
                  {idx > 0 && arr[idx - 1] !== p - 1 && <span className="px-1">...</span>}
                  <button
                    onClick={() => fetchData(p)}
                    className={`px-3 py-1 rounded-lg border ${
                      p === meta.current_page ? "bg-blue-600 text-white" : ""
                    }`}
                  >
                    {p}
                  </button>
                </span>
              ))}
            <button
              disabled={meta.current_page >= meta.last_page}
              onClick={() => fetchData(meta.current_page + 1)}
              className="px-3 py-1 rounded-lg border disabled:opacity-50"
            >
              Next
            </button>
          </div>
        </div>
      )}
        
      {modal && (
          <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div className="bg-white rounded-2xl shadow-2xl w-[520px] max-h-[90vh] overflow-y-auto p-6">
              <div className="flex items-center justify-between mb-4">
                <h2 className="text-xl font-bold">{editId ? "Edit" : "Tambah"} Mahasiswa</h2>
                <button onClick={() => { setModal(false); setError(""); }}><FaTimes /></button>
              </div>
              {error && <div className="bg-red-100 text-red-600 p-3 rounded-xl mb-4">{error}</div>}
              <form onSubmit={handleSubmit(onSubmit)}>
                <div className="grid grid-cols-2 gap-4">
                  <div className="col-span-2">
                    <label className="block mb-1 text-sm text-slate-600">Nama</label>
                    <input {...register("nama")} className="w-full border border-slate-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                  </div>
                  <div>
                    <label className="block mb-1 text-sm text-slate-600">NIM</label>
                    <input {...register("nim")} className="w-full border border-slate-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                  <label className="block mb-1 text-sm text-slate-600">Jenis Kelamin</label>
                  <select {...register("jenis_kelamin")} className="w-full border border-slate-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                  </select>
                </div>
                <div>
                  <label className="block mb-1 text-sm text-slate-600">Jurusan</label>
                  <select {...register("jurusan")} className="w-full border border-slate-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Pilih Jurusan</option>
                    <option>Teknik Informatika</option>
                    <option>Sistem Informasi</option>
                    <option>Ilmu Komputer</option>
                    <option>Teknik Elektro</option>
                    <option>Manajemen Informatika</option>
                    <option>Teknik Mesin</option>
                    <option>Teknik Sipil</option>
                    <option>Ekonomi Manajemen</option>
                    <option>Akuntansi</option>
                    <option>Hukum</option>
                  </select>
                </div>
                <div>
                  <label className="block mb-1 text-sm text-slate-600">Fakultas</label>
                  <select {...register("fakultas")} className="w-full border border-slate-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Pilih Fakultas</option>
                    <option>Fakultas Teknik</option>
                    <option>Fakultas Ilmu Komputer</option>
                    <option>Fakultas Ekonomi dan Bisnis</option>
                    <option>Fakultas Hukum</option>
                    <option>Fakultas Ilmu Sosial</option>
                  </select>
                </div>
                <div className="col-span-2">
                  <label className="block mb-1 text-sm text-slate-600">Email</label>
                  <input type="email" {...register("email")} className="w-full border border-slate-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                  <label className="block mb-1 text-sm text-slate-600">Nomor HP</label>
                  <input {...register("nomor_hp")} placeholder="08xxxxxxxxxx" className="w-full border border-slate-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                  <label className="block mb-1 text-sm text-slate-600">Tanggal Lahir</label>
                  <input type="date" {...register("tanggal_lahir")} className="w-full border border-slate-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div className="col-span-2">
                  <label className="block mb-1 text-sm text-slate-600">Alamat</label>
                  <textarea {...register("alamat")} rows={2} className="w-full border border-slate-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
              </div>
              <div className="flex gap-2 mt-6">
                <button type="submit" className="flex-1 py-2 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700">
                  Simpan
                </button>
                <button type="button" onClick={() => { setModal(false); setError(""); }} className="px-4 py-2 border rounded-xl hover:bg-slate-100">
                  Batal
                </button>
              </div>
            </form>
            </div>
          </div>
        )}

      {deleting && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
          <div className="bg-white rounded-2xl shadow-2xl w-[400px] p-6 text-center">
            <h2 className="text-xl font-bold mb-4">Hapus Data?</h2>
            <p className="text-slate-500 mb-6">Data yang dihapus tidak bisa dikembalikan.</p>
            <div className="flex gap-2 justify-center">
              <button onClick={confirmDelete} className="px-6 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700">
                Hapus
              </button>
              <button onClick={() => setDeleting(null)} className="px-6 py-2 border rounded-xl hover:bg-slate-100">
                Batal
              </button>
            </div>
          </div>
        </div>
      )}
    </DashboardLayout>
  );
}
