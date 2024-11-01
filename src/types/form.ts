import { z } from "zod";

const DisplayOptionEnum = z.enum(["global", "hide", "show"]);

const displayOptionsSchema = z.object({
  global: z.enum(["show", "hide"]),
  home: DisplayOptionEnum,
  posts: DisplayOptionEnum,
  pages: DisplayOptionEnum,
  archive: DisplayOptionEnum,
  category: DisplayOptionEnum,
  notFound: DisplayOptionEnum.describe("404 page"),
});

export const formSchema = z.object({
  organizationId: z.string().min(1),
  siteId: z.string().min(1),
  defaultChatId: z.string().min(1),
  displayOptions: displayOptionsSchema,
  position: z.enum(["in-page", "bottom", "aside", "overlay"]),
});

export interface Organization {
  id: string;
  name: string;
}

export interface Site {
  id: string;
  name: string;
  slug: string;
}

export interface Chat {
  id: string;
  name: string;
  slug: string;
}
