import { useQuery } from "@tanstack/react-query";
import { fetchChats } from "../services/chats";

export function useChats(siteId: string) {
  const { data, isLoading, error } = useQuery({
    queryKey: ["chats", siteId],
    queryFn: () => (siteId ? fetchChats(siteId) : []),
  });

  return { chats: data, isLoading, error };
}
