import React from "react";
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
import { RadioGroup, RadioGroupItem } from "./ui/radio-group";

interface PositionSelectProps {
  form: UseFormReturn<z.infer<typeof formSchema>>;
}

const positions = [
  { value: "in-page", label: "In Page" },
  { value: "bottom", label: "Bottom" },
  { value: "aside", label: "Aside" },
  { value: "overlay", label: "Overlay" },
];

export default function PositionSelect({ form }: PositionSelectProps) {
  return (
    <FormField
      control={form.control}
      name="position"
      render={({ field }) => (
        <FormItem>
          <FormLabel>Position</FormLabel>
          <FormControl>
            <RadioGroup
              onValueChange={field.onChange}
              value={field.value}
              className="tw-flex tw-flex-col tw-space-y-1"
            >
              {positions.map((position) => (
                <FormItem
                  key={position.value}
                  className="tw-flex tw-items-center tw-space-x-3 tw-space-y-0"
                >
                  <FormControl>
                    <RadioGroupItem value={position.value} />
                  </FormControl>
                  <FormLabel className="tw-font-normal">
                    {position.label}
                  </FormLabel>
                </FormItem>
              ))}
            </RadioGroup>
          </FormControl>
          <FormDescription>
            Select the position where the chat widget will appear on your
            website.
          </FormDescription>
          <FormMessage />
        </FormItem>
      )}
    />
  );
}
