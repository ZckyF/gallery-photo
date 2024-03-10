const convertDate = (createdStr: string): string => {
  const now: any = new Date();
  const createdAt: any = new Date(createdStr); // Konversi string ke objek Date
  const diffMs = now - createdAt;
  // Hitung perbedaan waktu dalam detik, menit, jam, dan hari
  const diffSeconds = Math.floor(diffMs / 1000);
  const diffMinutes = Math.floor(diffSeconds / 60);
  const diffHours = Math.floor(diffMinutes / 60);
  const diffDays = Math.floor(diffHours / 24);

  // Format berdasarkan perbedaan waktu
  if (diffDays > 7) {
    // Jika lebih dari 7 hari, gunakan format tanggal
    return createdAt.toLocaleDateString();
  } else if (diffDays > 1) {
    return `${diffDays} days ago`;
  } else if (diffDays === 1) {
    return '1 day ago';
  } else if (diffHours > 1) {
    return `${diffHours} hours ago`;
  } else if (diffHours === 1) {
    return '1 hour ago';
  } else if (diffMinutes > 1) {
    return `${diffMinutes} minutes ago`;
  } else if (diffMinutes === 1) {
    return '1 minute ago';
  } else {
    return 'Just now';
  }
};

const convertFollow = (value: number | string): string => {
  if (typeof value === 'number') {
    if (value >= 1000) {
      const suffixes = ['', 'K', 'M', 'B', 'T', 'P', 'E'];
      const valueString = value.toString();
      const suffixNum = Math.floor((valueString.length - 1) / 3);

      let shortValue: number | string = parseFloat((suffixNum !== 0 ? value / Math.pow(1000, suffixNum) : value).toFixed(0));

      return shortValue + suffixes[suffixNum];
    }
  }

  return value.toString();
};

export { convertDate, convertFollow };
