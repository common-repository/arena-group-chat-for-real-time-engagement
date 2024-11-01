import * as React from "react";
import * as TooltipPrimitive from "@radix-ui/react-tooltip";

import { cn } from "../../lib/utils";

const TooltipProvider = TooltipPrimitive.Provider;

const Tooltip = TooltipPrimitive.Root;

const TooltipTrigger = TooltipPrimitive.Trigger;

const TooltipContent = React.forwardRef<
  React.ElementRef<typeof TooltipPrimitive.Content>,
  React.ComponentPropsWithoutRef<typeof TooltipPrimitive.Content>
>(({ className, sideOffset = 4, ...props }, ref) => (
  <TooltipPrimitive.Content
    ref={ref}
    sideOffset={sideOffset}
    className={cn(
      "tw-z-50 tw-overflow-hidden tw-rounded-md tw-border tw-border-neutral-200 tw-bg-white tw-px-3 tw-py-1.5 tw-text-sm tw-text-neutral-950 tw-shadow-md tw-animate-in tw-fade-in-0 tw-zoom-in-95 tw-data-[state=closed]:tw-animate-out tw-data-[state=closed]:tw-fade-out-0 tw-data-[state=closed]:tw-zoom-out-95 tw-data-[side=bottom]:tw-slide-in-from-top-2 tw-data-[side=left]:tw-slide-in-from-right-2 tw-data-[side=right]:tw-slide-in-from-left-2 tw-data-[side=top]:tw-slide-in-from-bottom-2 tw-dark:tw-border-neutral-800 tw-dark:tw-bg-neutral-950 tw-dark:tw-text-neutral-50",
      className
    )}
    {...props}
  />
));
TooltipContent.displayName = TooltipPrimitive.Content.displayName;

export { Tooltip, TooltipTrigger, TooltipContent, TooltipProvider };
