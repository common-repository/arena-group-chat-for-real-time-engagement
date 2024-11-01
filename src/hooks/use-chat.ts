import { useChats } from "./use-chats";
import { Chat } from "../types/form";
import { useMemo } from "react";

export function useChat(siteId: string, chatId: string) {
  const { chats, isLoading, error } = useChats(siteId);

  const chat = useMemo(
    () => chats?.find((chat: Chat) => chat.id === chatId),
    [chats, chatId]
  );

  return { chat, isLoading, error };
}
