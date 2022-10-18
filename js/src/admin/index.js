import app from 'flarum/admin/app';
import LineOAuthPage from "./components/LineOAuthPage";

app.initializers.add('ianm-oauth-line', () => {
  app.extensionData.for('ianm-oauth-line').registerPage(LineOAuthPage);
});
