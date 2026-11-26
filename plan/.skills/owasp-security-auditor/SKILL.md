---
name: owasp-security-auditor
description: Audit code, APIs, architectures, and configs against the OWASP Top 10:2025 security categories and provide concrete remediation. Use when the user asks for "owasp", "security audit", "vulnerability check", "penetration check", or wants to secure web apps and APIs.
---
# OWASP Security Auditor

Audit application code, endpoints, configurations, and system designs against the **OWASP Top 10:2025** standard and provide actionable, code-level fixes.

## When to Use

- User mentions "owasp", "security audit", "cek keamanan", "vulnerability", or "hardening"
- Reviewing authentication, authorization, database queries, file uploads, external fetches, or deployment configs
- Designing new APIs or features that handle sensitive data, tokens, or user input

## Instructions

1. **Match Prompt Language:** Respond in Indonesian or English matching the user's prompt.
2. **Audit Against OWASP Top 10:2025 Categories:**
   - **A01:2025 Broken Access Control (includes SSRF):** Missing object/function-level authorization (IDOR/BOLA), privilege escalation, CORS misconfigurations, or unvalidated server-side URL fetches (SSRF).
   - **A02:2025 Security Misconfiguration:** Debug mode enabled, verbose stack traces, default credentials, overly permissive cloud/IAM roles, missing security headers.
   - **A03:2025 Software Supply Chain Failures:** Unpinned dependencies, untrusted packages, compromised CI/CD pipelines, missing lockfile/integrity checks.
   - **A04:2025 Cryptographic Failures:** Plaintext secrets/tokens, weak hashing (MD5/SHA1 for passwords instead of Argon2id/bcrypt), hardcoded keys, unencrypted sensitive transport/storage.
   - **A05:2025 Injection:** SQL/NoSQL injection, OS command injection, XSS, SSTI, or unsanitized LLM/template interpolation.
   - **A06:2025 Insecure Design:** Missing rate limits, lack of idempotency on financial/critical actions, trust-client business logic flaws.
   - **A07:2025 Authentication Failures:** Weak session/JWT handling (missing expiry, `alg: none`, tokens in `localStorage` without XSS defense), missing MFA/brute-force protection.
   - **A08:2025 Software or Data Integrity Failures:** Insecure deserialization, unsigned webhooks/updates, unverified CI artifacts.
   - **A09:2025 Security Logging and Alerting Failures:** Missing audit logs on auth/admin/access denials, or logging sensitive PII/passwords/tokens in plaintext.
   - **A10:2025 Mishandling of Exceptional Conditions:** Failing open on catch blocks, unhandled promise rejections, leaking internal state on abnormal errors.
3. **Structured Finding Output:**
   - Report only findings relevant to the provided code/design, ordered by severity (**Critical / High / Medium / Low**).
   - Format each finding as: **[OWASP ID & Category]** -> **Vulnerable Vector** -> **Exploit Impact** -> **Secure Fix (Code/Config)**.
   - If the code is clean for the checked surface, state clearly which OWASP categories were verified and any assumptions made.

## Gotchas

- Always reference the **2025** mapping (for example, SSRF belongs under **A01:2025 Broken Access Control**, and error/fail-open handling belongs under **A10:2025 Mishandling of Exceptional Conditions**).
- Never provide functional exploit payloads targeting real external systems; keep examples strictly defensive and educational.
