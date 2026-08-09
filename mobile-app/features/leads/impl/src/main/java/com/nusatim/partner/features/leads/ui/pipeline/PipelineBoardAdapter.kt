package com.nusatim.partner.features.leads.ui.pipeline

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.nusatim.partner.core.model.dto.LeadResponse
import com.nusatim.partner.features.leads.databinding.ItemPipelineColumnBinding

data class PipelineColumn(
    val title: String,
    val status: String,
    val leads: List<LeadResponse>
)

class PipelineBoardAdapter(
    private val onLeadClick: (LeadResponse) -> Unit,
    private val onLeadLongClick: (LeadResponse) -> Unit
) : RecyclerView.Adapter<PipelineBoardAdapter.ViewHolder>() {

    private var columns: List<PipelineColumn> = emptyList()
    private var expandedPosition: Int = 0 // Default expand the first one

    fun submitColumns(newColumns: List<PipelineColumn>) {
        columns = newColumns
        notifyDataSetChanged()
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemPipelineColumnBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(columns[position], position == expandedPosition)
    }

    override fun getItemCount(): Int = columns.size

    inner class ViewHolder(private val binding: ItemPipelineColumnBinding) : RecyclerView.ViewHolder(binding.root) {
        init {
            binding.cardColumnHeader.setOnClickListener {
                val position = adapterPosition
                if (position != RecyclerView.NO_POSITION) {
                    if (expandedPosition != position) {
                        val prevExpanded = expandedPosition
                        expandedPosition = position
                        notifyItemChanged(prevExpanded)
                        notifyItemChanged(expandedPosition)
                    }
                }
            }
        }

        fun bind(column: PipelineColumn, isExpanded: Boolean) {
            binding.tvColumnTitle.text = column.title
            binding.tvColumnCount.text = column.leads.size.toString()
            
            // Dynamic Header Styling
            val (bgColor, iconBg, iconTint, iconRes, countBg, countTint) = when (column.status.lowercase()) {
                "new" -> Hexuple(
                    com.nusatim.partner.core.ui.R.color.project_orange_light,
                    "#26A6541A",
                    com.nusatim.partner.core.ui.R.color.partner_primary,
                    android.R.drawable.btn_star_big_on,
                    com.nusatim.partner.core.ui.R.drawable.bg_badge_blue_light, // Reuse for shape
                    com.nusatim.partner.core.ui.R.color.partner_primary
                )
                "contacted" -> Hexuple(
                    com.nusatim.partner.core.ui.R.color.project_blue_light,
                    "#260061A4",
                    com.nusatim.partner.core.ui.R.color.status_info,
                    android.R.drawable.ic_menu_call,
                    com.nusatim.partner.core.ui.R.drawable.bg_badge_blue_light,
                    com.nusatim.partner.core.ui.R.color.status_info
                )
                "qualified" -> Hexuple(
                    "#F3E5F5", // Purple light
                    "#266A1BA1",
                    "#6A1BA1",
                    android.R.drawable.ic_menu_agenda,
                    com.nusatim.partner.core.ui.R.drawable.bg_badge_blue_light,
                    "#6A1BA1"
                )
                "opportunity" -> Hexuple(
                    "#E8F5E9", // Green light
                    "#26006D39",
                    com.nusatim.partner.core.ui.R.color.status_success,
                    android.R.drawable.ic_menu_add,
                    com.nusatim.partner.core.ui.R.drawable.bg_badge_blue_light,
                    com.nusatim.partner.core.ui.R.color.status_success
                )
                "proposal" -> Hexuple(
                    "#FFF3E0", // Orange deep light
                    "#26E65100",
                    "#E65100",
                    android.R.drawable.ic_menu_save,
                    com.nusatim.partner.core.ui.R.drawable.bg_badge_blue_light,
                    "#E65100"
                )
                "negotiation" -> Hexuple(
                    "#FFEBEE", // Red light
                    "#26C62828",
                    com.nusatim.partner.core.ui.R.color.partner_error,
                    android.R.drawable.ic_menu_share,
                    com.nusatim.partner.core.ui.R.drawable.bg_badge_blue_light,
                    com.nusatim.partner.core.ui.R.color.partner_error
                )
                else -> Hexuple(
                    com.nusatim.partner.core.ui.R.color.partner_surface_variant,
                    "#26787579",
                    com.nusatim.partner.core.ui.R.color.status_neutral,
                    android.R.drawable.ic_menu_help,
                    com.nusatim.partner.core.ui.R.drawable.bg_badge_blue_light,
                    com.nusatim.partner.core.ui.R.color.status_neutral
                )
            }

            val context = binding.root.context
            binding.cardColumnHeader.backgroundTintList = android.content.res.ColorStateList.valueOf(
                if (bgColor is String) android.graphics.Color.parseColor(bgColor) 
                else androidx.core.content.ContextCompat.getColor(context, bgColor as Int)
            )
            binding.cardStatusIcon.backgroundTintList = android.content.res.ColorStateList.valueOf(android.graphics.Color.parseColor(iconBg))
            binding.ivStatusIcon.setImageResource(iconRes)
            binding.ivStatusIcon.imageTintList = android.content.res.ColorStateList.valueOf(
                if (iconTint is String) android.graphics.Color.parseColor(iconTint)
                else androidx.core.content.ContextCompat.getColor(context, iconTint as Int)
            )
            
            binding.tvColumnCount.backgroundTintList = android.content.res.ColorStateList.valueOf(
                android.graphics.Color.parseColor("#20${if (countTint is String) countTint.removePrefix("#") else Integer.toHexString(androidx.core.content.ContextCompat.getColor(context, countTint as Int)).substring(2)}")
            )
            binding.tvColumnCount.setTextColor(
                if (countTint is String) android.graphics.Color.parseColor(countTint)
                else androidx.core.content.ContextCompat.getColor(context, countTint as Int)
            )

            // Accent Line Color
            binding.viewColumnAccent.setBackgroundColor(
                if (iconTint is String) android.graphics.Color.parseColor(iconTint)
                else androidx.core.content.ContextCompat.getColor(context, iconTint as Int)
            )

            // Leads Visibility & Rotation
            binding.rvLeads.visibility = if (isExpanded) android.view.View.VISIBLE else android.view.View.GONE
            binding.ivExpand.rotation = if (isExpanded) 180f else 0f
            binding.ivExpand.setImageResource(com.nusatim.partner.core.ui.R.drawable.ic_chevron_right)
            
            if (isExpanded) {
                val cardAdapter = PipelineCardAdapter(column.status, onLeadClick, onLeadLongClick)
                binding.rvLeads.layoutManager = LinearLayoutManager(binding.root.context)
                binding.rvLeads.adapter = cardAdapter
                cardAdapter.submitList(column.leads)
            }
        }
    }

    private data class Hexuple<out A, out B, out C, out D, out E, out F>(
        val first: A, val second: B, val third: C, val fourth: D, val fifth: E, val sixth: F
    )
}
