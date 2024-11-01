import React, { useState, useEffect } from "react";
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
import { useOrganizations } from "../hooks/use-organizations";
import { Building } from "lucide-react";

interface OrganizationSelectProps {
  form: UseFormReturn<z.infer<typeof formSchema>>;
}

export default function OrganizationSelect({ form }: OrganizationSelectProps) {
  const { organizations, isLoading, error } = useOrganizations();

  if (isLoading) return <p>Loading organizations...</p>;
  if (error) return <p>Error: {error.message}</p>;

  return (
    <FormField
      control={form.control}
      name="organizationId"
      render={({ field }) => (
        <FormItem>
          <FormLabel>Organization</FormLabel>
          <FormControl>
            <Select onValueChange={field.onChange} value={field.value}>
              <SelectTrigger className="tw-w-full">
                <SelectValue placeholder="Select an organization" />
              </SelectTrigger>
              <SelectContent>
                {organizations?.map((org) => (
                  <SelectItem key={org.id} value={org.id}>
                    <div className="tw-flex tw-items-center">
                      <Building className="tw-mr-2 tw-h-4 tw-w-4" />
                      <span>{org.name}</span>
                    </div>
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </FormControl>
          <FormDescription>
            Select the organization you want to configure.
          </FormDescription>
          <FormMessage />
        </FormItem>
      )}
    />
  );
}
