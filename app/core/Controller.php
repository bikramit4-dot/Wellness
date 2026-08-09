<?php
abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        $viewFile = APP_ROOT . '/app/views/' . $view . '.php';
        $layout = $data['layout'] ?? 'default';
        unset($data['layout']);

        // Only allow layouts that actually exist (prevents path traversal).
        $allowedLayouts = ['default', 'admin'];
        if (!in_array($layout, $allowedLayouts, true)) {
            $layout = 'default';
        }

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        extract($data, EXTR_SKIP);
        require APP_ROOT . '/app/views/layouts/' . $layout . '.php';
    }

    protected function redirect(string $path): void
    {
        $location = (BASE_URL ?? '') . $path;
        header('Location: ' . $location);
        exit;
    }

    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }
}
