// graphify OpenCode plugin (OpenCode v2 format)
// Injects a knowledge graph reminder before bash tool calls when the graph exists.
//
// IMPORTANT: keep the reminder string free of backticks and $(...) constructs.
// The hook prepends `echo "<reminder>" ; <cmd>` to the user's bash command;
// backticks inside the double-quoted echo trigger bash command substitution,
// which both corrupts tool output and silently executes the very graphify
// command we are only suggesting. Plain words render fine in opencode's TUI.
//
// NOTE: do NOT import from "@opencode/plugin" here. That package is not
// installed in this project's node_modules, so the loader fails with
// "Cannot find package '@opencode/plugin'" / "Missing key at [\"default\"]"
// and opencode shows the plugin as failed in the Plugins panel. A plain
// default export with `{ id, setup }` is a valid V2 local plugin.
import { existsSync } from "fs";
import { join } from "path";

export default {
  id: "graphify",

  async setup(ctx) {
    const directory = ctx.location.directory;
    let reminded = false;

    await ctx.tool.hook("execute.before", (event) => {
      if (reminded) return;
      if (event.tool !== "bash" && event.tool !== "shell") return;
      if (typeof event.input?.command !== "string") return;
      if (!existsSync(join(directory, "graphify-out", "graph.json"))) return;

      // ';' not '&&' — Windows PowerShell 5.1 rejects '&&' as a statement
      // separator, breaking the first bash command of the session (#1646).
      event.input.command =
        'echo "[graphify] knowledge graph at graphify-out/. For focused questions, run graphify query with your question (scoped subgraph, usually much smaller than GRAPH_REPORT.md) instead of grepping raw files. Read GRAPH_REPORT.md only for broad architecture context." ; ' +
        event.input.command;
      reminded = true;
    });
  },
};
