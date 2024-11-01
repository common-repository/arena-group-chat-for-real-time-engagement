import React, { useState, useEffect, useRef } from "react";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "./ui/select";
import { UseFormReturn } from "react-hook-form";
import { z } from "zod";
import { formSchema } from "../types/form";
import {
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "./ui/form";
import { useChats } from "../hooks/use-chats";
import { MessageSquare } from "lucide-react";

interface Chat {
  id: string;
  name: string;
  slug: string;
}

interface DefaultChatSelectProps {
  form: UseFormReturn<z.infer<typeof formSchema>>;
}

export default function DefaultChatSelect({ form }: DefaultChatSelectProps) {
  const { chats, isLoading, error } = useChats(form.watch("siteId"));
  const firstLoad = useRef(true);

  useEffect(() => {
    if (!firstLoad.current) {
      firstLoad.current = false;
      form.setValue("defaultChatId", chats?.[0]?.id || "");
    }
  }, [form.watch("siteId")]);

  if (isLoading) return <p>Loading chats...</p>;
  if (error) return <p>Error: {error.message}</p>;

  return (
    <FormField
      control={form.control}
      name="defaultChatId"
      render={({ field }) => (
        <FormItem>
          <FormLabel>Default Chat</FormLabel>
          <FormControl>
            <Select
              onValueChange={field.onChange}
              value={field.value || ""}
              disabled={!form.watch("siteId")}
            >
              <SelectTrigger>
                <SelectValue placeholder="Select a default chat" />
              </SelectTrigger>
              <SelectContent>
                {chats?.map((chat) => (
                  <SelectItem key={chat.id} value={chat.id}>
                    <div className="tw-flex tw-items-center">
                      <MessageSquare className="tw-w-4 tw-h-4 tw-mr-2" />
                      <span>{chat.name}</span>
                    </div>
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </FormControl>
          <FormDescription>
            Select the default chat you want to use.
          </FormDescription>
          <FormMessage />
        </FormItem>
      )}
    />
  );
}
