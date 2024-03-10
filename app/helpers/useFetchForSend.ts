const useFetchForSend = async (path: string, data: Object, method: string, useToken: boolean = true) => {
  const url = useRuntimeConfig().public.URL_API;

  // Stringify data jika metode adalah POST atau PUT
  const body = method === 'POST' || method === 'PUT' ? JSON.stringify(data) : undefined;

  const headers = {
    'Content-Type': 'application/json',
    Authorization: useToken ? `Bearer YOUR_TOKEN_HERE` : '',
  };

  const response = await $fetch(`${url}/${path}`, {
    method: method,
    headers: headers,
    body: body,
  });

  const responseData = await response.json();

  return responseData;
};

export { useFetchForSend };
