import app from 'flarum/admin/app';
import { ConfigureWithOAuthPage } from '@fof-oauth';

app.initializers.add('ianm-oauth-line', () => {
  app.extensionData.for('ianm-oauth-line').registerPage(ConfigureWithOAuthPage);
});
