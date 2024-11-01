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
import { useSites } from "../hooks/use-sites";
import { Monitor } from "lucide-react";

interface Site {
  id: string;
  name: string;
}

interface SiteSelectProps {
  form: UseFormReturn<z.infer<typeof formSchema>>;
}

export default function SiteSelect({ form }: SiteSelectProps) {
  const { sites, isLoading, error } = useSites(form.watch("organizationId"));
  const firstLoad = useRef(true);

  useEffect(() => {
    if (!firstLoad.current) {
      firstLoad.current = false;
      form.setValue("siteId", sites?.[0]?.id || "");
    }
  }, [form.watch("organizationId")]);

  if (isLoading) return <p>Loading sites...</p>;
  if (error) return <p>Error: {error.message}</p>;

  return (
    <FormField
      control={form.control}
      name="siteId"
      render={({ field }) => (
        <FormItem>
          <FormLabel>Site</FormLabel>
          <FormControl>
            <Select
              onValueChange={field.onChange}
              value={field.value || ""}
              disabled={!form.watch("organizationId") || sites?.length === 0}
            >
              <SelectTrigger className="tw-w-full">
                <SelectValue placeholder="Select a site" />
              </SelectTrigger>
              <SelectContent>
                {sites?.map((site) => (
                  <SelectItem key={site.id} value={site.id}>
                    <div className="tw-flex tw-items-center">
                      <Monitor className="tw-mr-2 tw-h-4 tw-w-4" />
                      <span>{site.name}</span>
                    </div>
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </FormControl>
          <FormDescription>
            Select the site you want to configure.
          </FormDescription>
          <FormMessage />
        </FormItem>
      )}
    />
  );
}
