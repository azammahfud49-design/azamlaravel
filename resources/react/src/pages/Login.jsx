<div className="relative z-10 w-full max-w-6xl px-8">

  <div className="grid lg:grid-cols-2 gap-10 items-center">

    {/* KIRI */}
    <div className="hidden lg:block text-white">

      <h1 className="text-6xl font-bold mb-6">
        Sistem Akademik
      </h1>

      <p className="text-2xl text-white/90 mb-8">
        Kelola data mahasiswa secara modern,
        cepat, aman dan profesional.
      </p>

      <div className="grid grid-cols-2 gap-4">

        <div className="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl p-5">
          <h3 className="font-bold text-xl">
            📚 Data Mahasiswa
          </h3>
          <p className="text-sm text-white/80 mt-2">
            Kelola seluruh data akademik mahasiswa.
          </p>
        </div>

        <div className="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl p-5">
          <h3 className="font-bold text-xl">
            📊 Statistik
          </h3>
          <p className="text-sm text-white/80 mt-2">
            Monitoring data secara realtime.
          </p>
        </div>

      </div>

    </div>

    {/* FORM LOGIN */}
    <div className="backdrop-blur-xl bg-white/10 border border-white/20 shadow-2xl rounded-3xl p-10">

      <h2 className="text-5xl font-bold text-white text-center mb-2">
        LOGIN
      </h2>

      <p className="text-center text-white/80 mb-8">
        Selamat datang kembali
      </p>

      {error && (
        <div className="bg-red-500/20 border border-red-400 text-red-100 p-3 rounded-xl mb-4">
          {error}
        </div>
      )}

      <div className="mb-4">
        <input
          {...register("email")}
          placeholder="Email"
          className="
            w-full
            px-5
            py-4
            rounded-xl
            bg-white/10
            border
            border-white/20
            text-white
            placeholder-white/60
            focus:outline-none
            focus:ring-2
            focus:ring-green-400
          "
        />
      </div>

      <div className="mb-6">
        <input
          {...register("password")}
          type="password"
          placeholder="Password"
          className="
            w-full
            px-5
            py-4
            rounded-xl
            bg-white/10
            border
            border-white/20
            text-white
            placeholder-white/60
            focus:outline-none
            focus:ring-2
            focus:ring-green-400
          "
        />
      </div>

      <button
        type="submit"
        disabled={loading}
        className="
          w-full
          py-4
          rounded-xl
          bg-gradient-to-r
          from-green-500
          to-emerald-600
          text-white
          font-bold
          text-lg
          hover:scale-105
          transition
        "
      >
        {loading ? "Memproses..." : "Masuk"}
      </button>

      <p className="text-center text-white/80 mt-6">
        Belum punya akun?
        <Link
          to="/register"
          className="ml-2 text-green-300 font-bold"
        >
          Daftar
        </Link>
      </p>

    </div>

  </div>

</div>