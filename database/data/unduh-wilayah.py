"""Unduh data wilayah satu provinsi dari wilayah.id jadi satu berkas JSON.

Dipakai kalau PHP di mesin ini belum bisa HTTPS (curl.cainfo kosong), sehingga
`php artisan wilayah:impor` tidak bisa menarik langsung. Hasilnya dibaca lewat:

    php artisan wilayah:impor --dari=database/data/wilayah-bali.json

Induk tiap baris tidak disimpan terpisah karena sudah tersirat di kodenya:
51.01.05.1005 (desa) -> 51.01.05 (kecamatan) -> 51.01 (kabupaten).

Jalankan:
    python database/data/unduh-wilayah.py 51 database/data/wilayah-bali.json
"""

import json
import pathlib
import ssl
import sys
import urllib.request

BASIS = "https://wilayah.id/api"
DIREKTORI_DASAR = pathlib.Path(__file__).resolve().parent


def validasi_tujuan(tujuan):
    jalur = pathlib.Path(tujuan).resolve()
    if not jalur.is_relative_to(DIREKTORI_DASAR):
        raise ValueError(f"tujuan harus di dalam {DIREKTORI_DASAR}, dapat: {jalur}")
    return jalur


def ambil(jalur):
    url = f"{BASIS}/{jalur}.json"
    konteks = ssl.create_default_context()
    konteks.minimum_version = ssl.TLSVersion.TLSv1_2
    try:
        with urllib.request.urlopen(url, context=konteks, timeout=30) as resp:
            return json.loads(resp.read()).get("data", [])
    except urllib.error.HTTPError as e:
        if e.code == 404:
            return []
        raise


def main():
    provinsi = sys.argv[1] if len(sys.argv) > 1 else "51"
    tujuan = validasi_tujuan(
        sys.argv[2] if len(sys.argv) > 2 else "database/data/wilayah-bali.json"
    )

    hasil = {"provinsi": provinsi, "kabupaten": [], "kecamatan": [], "desa": []}

    kabupatens = ambil(f"regencies/{provinsi}")
    hasil["kabupaten"] = kabupatens
    print(f"kabupaten: {len(kabupatens)}")

    for kab in kabupatens:
        kecamatans = ambil(f"districts/{kab['code']}")
        hasil["kecamatan"].extend(kecamatans)

        for kec in kecamatans:
            hasil["desa"].extend(ambil(f"villages/{kec['code']}"))

        print(f"  {kab['name']}: {len(kecamatans)} kecamatan")

    with open(tujuan, "w", encoding="utf-8") as f:
        json.dump(hasil, f, ensure_ascii=False, indent=1)

    print(f"\nkecamatan: {len(hasil['kecamatan'])}, desa: {len(hasil['desa'])}")
    print(f"tersimpan di {tujuan}")


if __name__ == "__main__":
    main()
