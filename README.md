# 📄 Symfony PDF Generator (Chrome Headless)

This project provides a Symfony 7 console command that generates PDF files from HTML using Google Chrome in a Dockerized PHP-FPM environment.

## ✅ Requirements

- Docker and Docker Compose
- Chrome installed in the php-fpm container (already configured in the Dockerfile)

## 🐳 Build and start the containers

From the project root:

- Build and start the stack:
  ```bash
  make init
  ```

## 🧰 Console command

The command is registered as:

- `app:generate-pdf`

### ▶️ Usage

```
php bin/console app:generate-pdf <input> <output> [--chrome-bin=...] [--timeout=...] [--no-sandbox]
```

### 🧪 Examples

- Generate a PDF from a project-relative HTML file:

```
php bin/console app:generate-pdf documents/markup/index.html var/generated-pdf/output.pdf --no-sandbox
```

- Use an absolute input path:

```
php bin/console app:generate-pdf /application/documents/markup/robert/html/index.html /application/var/generated-pdf/output.pdf --no-sandbox
```

### ⚙️ Options

- `--chrome-bin` (default: `google-chrome-stable`)
  - Path or name of the Chrome binary in the container.
- `--timeout` (default: `60`)
  - Process timeout in seconds. Use a higher value for large pages.
- `--no-sandbox`
  - Disables Chrome sandbox (often required in containers).

## 🐋 Docker usage

Run the command inside the php-fpm container:

```
docker exec pdfsaver-php-fpm php bin/console app:generate-pdf some-example-folder/index.html var/generated-pdf/output.pdf --no-sandbox
```

## 🧩 Controller usage (rendered HTML)

You can also generate a PDF from rendered HTML inside a controller by calling `ChromePdfGenerator::generateFromHtml()`.

Example (simplified):

```php
use App\Service\ChromePdfGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class PdfController extends AbstractController
{
  public function generate(ChromePdfGenerator $generator): Response
  {
    $html = $this->renderView('pdf/example.html.twig', [
      'title' => 'PDF Example',
    ]);

    $outputPath = $this->getParameter('kernel.project_dir') . '/var/generated-pdf/output.pdf';

    $generator->generateFromHtml(
      html: $html,
      outputPath: $outputPath,
      noSandbox: true,
    );

    return new Response('PDF generated');
  }
}
```

## 📁 Output location

The output PDF path can be relative to the project root or an absolute path. If the output directory does not exist, it will be created automatically.

A common location is:

- `var/generated-pdf/`

## 🧪 Tests

The command is covered by a test that generates a real PDF from a minimal HTML example and saves it to:

- `var/generated-pdf/output.pdf`

Run tests:

```bash
make test
```

If you want coverage:

```bash
make test-coverage
```
