package com.example.ro_id;

import androidx.activity.result.ActivityResultLauncher;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.content.ContextCompat;

import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.drawable.ColorDrawable;
import android.os.Build;
import android.os.Bundle;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;

import com.google.android.material.dialog.MaterialAlertDialogBuilder;
import com.journeyapps.barcodescanner.CaptureActivity;
import com.journeyapps.barcodescanner.ScanContract;
import com.journeyapps.barcodescanner.ScanOptions;

import org.json.JSONException;
import org.json.JSONObject;

public class MainActivity extends AppCompatActivity {

    Button button;
    Button btn_settings;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        Window window = this.getWindow();
        // clear FLAG_TRANSLUCENT_STATUS flag:
        window.clearFlags(WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS);
        // add FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS flag to the window
        window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
        // finally change the color
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            window.setStatusBarColor(ContextCompat.getColor(this,R.color.white));
        }

        button = findViewById(R.id.button);
        button.setOnClickListener(v->
        {
            scanCode();
        });
        btn_settings = findViewById(R.id.button2);
        btn_settings.setOnClickListener(v->
        {
            startActivity(new Intent(MainActivity.this, SettingsActivity.class));
        });
    }

    private void scanCode()
    {
        ScanOptions options = new ScanOptions();
        options.setBeepEnabled(false);
        options.setPrompt("Încadrați codul QR pe ecran");
        options.setOrientationLocked(true);
        options.setCaptureActivity(CaptureAct.class);
        barLauncher.launch(options);
    }

    ActivityResultLauncher<ScanOptions> barLauncher = registerForActivityResult(new ScanContract(), result -> {
        boolean ok = true;
        if(result.getContents() != null) {
            JSONObject jObject = null;
            try {
                jObject = new JSONObject(result.getContents());
            } catch (JSONException e) {
                ok = false;
                AlertDialog.Builder builder = new AlertDialog.Builder(MainActivity.this);
                builder.setTitle("Eroare");
                builder.setMessage("Codul scanat nu respectă formatul Ro-ID.");
                builder.setPositiveButton("OK", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialogInterface, int i) {
                        dialogInterface.dismiss();
                    }
                }).show();
            }
            if(ok==true) {
                String att_id = "", challenge = "";
                try {
                    att_id = jObject.getString("attempt_id");
                    challenge = jObject.getString("challenge");
                } catch (JSONException e) {
                    ok = false;
                    AlertDialog.Builder builder = new AlertDialog.Builder(MainActivity.this);
                    builder.setTitle("Eroare");
                    builder.setMessage("Codul scanat nu respectă formatul Ro-ID.");
                    builder.setPositiveButton("OK", new DialogInterface.OnClickListener() {
                        @Override
                        public void onClick(DialogInterface dialogInterface, int i) {
                            dialogInterface.dismiss();
                        }
                    }).show();
                }
                if (ok == true) {
                    /*AlertDialog.Builder builder = new AlertDialog.Builder(MainActivity.this);
                    builder.setTitle("Rezultat");
                    builder.setMessage("ID: " + att_id + "\nChallenge: " + challenge);
                    builder.setPositiveButton("OK", new DialogInterface.OnClickListener() {
                        @Override
                        public void onClick(DialogInterface dialogInterface, int i) {
                            dialogInterface.dismiss();
                        }
                    }).show();*/
                    Intent i = new Intent(MainActivity.this, GetCodeActivity.class);
                    Bundle bundle = new Bundle();
                    bundle.putString("att_id", att_id);
                    bundle.putString("challenge", challenge);
                    i.putExtras(bundle);
                    startActivity(i);
                }
            }
        }
        else
        {
            AlertDialog.Builder builder = new AlertDialog.Builder(MainActivity.this);
            builder.setTitle("Eroare");
            builder.setMessage("Codul nu a putut fi scanat.");
            builder.setPositiveButton("OK", new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialogInterface, int i) {
                    dialogInterface.dismiss();
                }
            }).show();
        }
    });
}