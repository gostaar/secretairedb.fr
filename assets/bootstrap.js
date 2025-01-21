import { Application } from "stimulus";
import { definitionsFromContext } from "stimulus/webpack-helpers";

const application = Application.start();

// Chargement des contrôleurs Stimulus (optionnel si vous en avez)
const context = require.context("./controllers", true, /\.js$/);
application.load(definitionsFromContext(context));